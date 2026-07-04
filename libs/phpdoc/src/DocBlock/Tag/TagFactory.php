<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag;

use TypeLang\PhpDoc\Exception\MalformedTagException;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Parser\Grammar\Context;
use TypeLang\PhpDoc\Parser\Grammar\Cursor;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;
use TypeLang\PhpDoc\Parser\Grammar\Grammar;

/**
 * Builds a tag from its name and suffix using the matching
 * {@see TagDefinitionInterface}.
 *
 * A tag with no registered definition becomes a plain {@see Tag} whose whole
 * suffix is its description.
 *
 * ```
 * $factory = new TagFactory(
 *     definitions: ['link' => new LinkTagDefinition()],
 *     grammar: $grammar,
 * );
 * ```
 *
 * @template-implements \IteratorAggregate<non-empty-string, TagDefinitionInterface>
 */
final readonly class TagFactory implements TagFactoryInterface, \IteratorAggregate
{
    /**
     * @var array<non-empty-string, TagDefinitionInterface>
     */
    private array $definitions;

    /**
     * @param iterable<non-empty-string, TagDefinitionInterface> $definitions
     */
    public function __construct(
        iterable $definitions = [],
        /**
         * The terminals the definitions reference.
         */
        private Grammar $grammar = new Grammar(),
    ) {
        $this->definitions = \iterator_to_array($definitions);
    }

    public function create(string $name, string $suffix, DescriptionParserInterface $descriptions): TagInterface
    {
        $definition = $this->definitions[$name] ?? null;

        // An unregistered tag keeps its whole suffix as a description.
        if ($definition === null) {
            return new Tag($name, $descriptions->tryParse($suffix));
        }

        return $this->apply($name, $suffix, $definition, $descriptions);
    }

    /**
     * @param non-empty-string $name
     * @throws MalformedTagException when the suffix does not match the grammar
     */
    private function apply(
        string $name,
        string $suffix,
        TagDefinitionInterface $definition,
        DescriptionParserInterface $descriptions,
    ): TagInterface {
        $cursor = new Cursor($suffix);
        $context = new Context($cursor, $this->grammar, $descriptions);
        $rule = $definition->rule;

        try {
            $rule->match($context);
        } catch (NoMatchException) {
            throw MalformedTagException::becauseTagBodyIsMalformed(
                tag: $name,
                grammar: (string) $rule,
                source: $suffix,
                offset: $cursor->furthestOffset,
            );
        }

        return $definition->create($name, $context->toMatchedResult());
    }

    public function getIterator(): \Traversable
    {
        /** @var \ArrayIterator<non-empty-string, TagDefinitionInterface> */
        return new \ArrayIterator($this->definitions);
    }

    public function count(): int
    {
        return \count($this->definitions);
    }
}
