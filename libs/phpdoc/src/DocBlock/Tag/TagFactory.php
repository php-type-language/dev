<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag;

use TypeLang\PhpDoc\Exception\MalformedTagException;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PhpDoc\Parser\Grammar\Context;
use TypeLang\PhpDoc\Parser\Grammar\Cursor;
use TypeLang\PhpDoc\Parser\Grammar\Exception\InvalidTagRuleException;
use TypeLang\PhpDoc\Parser\Grammar\Exception\InvalidTagRuleForDefinitionException;
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
 * @phpstan-import-type RuleType from Grammar
 *
 * @template-implements \IteratorAggregate<non-empty-string, TagDefinitionInterface>
 */
final readonly class TagFactory implements TagFactoryInterface, \IteratorAggregate
{
    /**
     * @var array<non-empty-string, TagDefinitionInterface>
     */
    private array $definitions;

    private Grammar $grammar;

    /**
     * @param iterable<non-empty-string, TagDefinitionInterface> $definitions
     * @param iterable<non-empty-string, RuleType> $rules
     */
    public function __construct(
        iterable $definitions = [],
        iterable $rules = [],
        private TagDefinitionInterface $genericTagDefinition = new GenericTagDefinition(),
    ) {
        $this->definitions = \iterator_to_array($definitions);
        $this->grammar = new Grammar($rules);
    }

    public function create(string $name, string $suffix, DescriptionParserInterface $descriptions): TagInterface
    {
        $definition = $this->definitions[$name]
            ?? $this->genericTagDefinition;

        $cursor = new Cursor($suffix);
        $context = new Context($cursor, $this->grammar);
        $rule = $definition->rule;

        try {
            $rule->match($context);
        } catch (InvalidTagRuleException $e) {
            throw InvalidTagRuleForDefinitionException::becauseInvalidRuleForDefinition(
                name: $e->name,
                definition: $definition,
                previous: $e,
            );
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
