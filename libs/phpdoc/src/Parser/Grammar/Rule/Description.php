<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser\Grammar\Rule;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\Parser\Grammar\Context;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;

/**
 * Matches the trailing description and captures it as a
 * {@see DescriptionInterface}.
 *
 * There is no description to capture when nothing is left, so wrap it in
 * {@see Optional} to make it optional.
 */
final class Description extends Rule
{
    public function __construct(
        private readonly ?string $alias = null,
    ) {}

    public function match(Context $context): void
    {
        $text = \rtrim($context->cursor->readRemainder());

        if ($text === '') {
            throw new NoMatchException('Expected a description');
        }

        $description = $context->descriptions->parse($text);

        if ($this->alias !== null) {
            $context->capture($this->alias, $description);
        }
    }

    public function __toString(): string
    {
        return '<description>';
    }
}
