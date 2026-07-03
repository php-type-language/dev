<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser\Grammar\Rule;

use TypeLang\PhpDoc\Parser\Grammar\Context;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;

/**
 * Matches an ordered sequence of rules, one after another.
 */
final class SequenceOf extends Rule
{
    /**
     * @var list<Rule>
     */
    private readonly array $rules;

    public function __construct(Rule ...$rules)
    {
        $this->rules = \array_values($rules);
    }

    public function match(Context $context): void
    {
        $snapshot = $context->mark();

        try {
            foreach ($this->rules as $rule) {
                $context->cursor->skipWhitespace();
                $rule->match($context);
            }
        } catch (NoMatchException $e) {
            $context->rollback($snapshot);

            throw $e;
        }
    }

    public function __toString(): string
    {
        return \implode(' ', \array_map(\strval(...), $this->rules));
    }
}
