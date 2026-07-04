<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser;

use TypeLang\PhpDoc\DocBlock\Tag\TagDefinitionInterface;
use TypeLang\PhpDoc\Exception\MalformedTagException;
use TypeLang\PhpDoc\Parser\Grammar\Context;
use TypeLang\PhpDoc\Parser\Grammar\Cursor;
use TypeLang\PhpDoc\Parser\Grammar\Exception\InvalidTagRuleException;
use TypeLang\PhpDoc\Parser\Grammar\Exception\InvalidTagRuleForDefinitionException;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;
use TypeLang\PhpDoc\Parser\Grammar\Grammar;
use TypeLang\PhpDoc\Parser\Grammar\MatchedResult;

/**
 * @phpstan-import-type RuleType from Grammar
 */
final readonly class TagParser
{
    private Grammar $grammar;

    /**
     * @param iterable<non-empty-string, RuleType> $rules
     */
    public function __construct(iterable $rules)
    {
        $this->grammar = new Grammar($rules);
    }

    public function parse(TagDefinitionInterface $definition, string $name, string $suffix): MatchedResult
    {
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

        return $context->toMatchedResult();
    }
}
