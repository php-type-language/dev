<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser\Grammar\Rule;

use TypeLang\PhpDoc\Parser\Grammar\Context;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;

/**
 * Matches an exact literal at the current position.
 */
final readonly class LiteralRule implements TerminalInterface
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        private string $literal,
        /**
         * @var non-empty-string|null
         */
        public ?string $alias = null,
    ) {}

    public function match(Context $context): void
    {
        if (!$context->cursor->readLiteral($this->literal)) {
            throw new NoMatchException(\sprintf('Expected "%s"', $this->literal));
        }

        if ($this->alias !== null) {
            $context->capture($this->alias, true);
        }
    }

    public function __toString(): string
    {
        return \sprintf('"%s"', $this->literal);
    }
}
