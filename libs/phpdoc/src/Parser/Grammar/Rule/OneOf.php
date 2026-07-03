<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser\Grammar\Rule;

use TypeLang\PhpDoc\Parser\Grammar\Context;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;

/**
 * Matches the first of the given alternatives that applies.
 */
final class OneOf extends Rule
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
        foreach ($this->rules as $rule) {
            $snapshot = $context->mark();

            try {
                $rule->match($context);

                return;
            } catch (NoMatchException) {
                $context->rollback($snapshot);
            }
        }

        throw new NoMatchException(\sprintf('Expected one of: %s', $this));
    }

    public function __toString(): string
    {
        return \sprintf('( %s )', \implode(' | ', \array_map(\strval(...), $this->rules)));
    }
}
