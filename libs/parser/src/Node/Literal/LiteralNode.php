<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

use TypeLang\Parser\Node\Stmt\TypeStatement;

/**
 * @template TValue of mixed = mixed
 * @template-implements LiteralNodeInterface<TValue>
 */
abstract class LiteralNode extends TypeStatement implements LiteralNodeInterface
{
    public function __construct(
        /**
         * @var TValue
         */
        public readonly mixed $value,
        public readonly string $raw,
    ) {}

    public function __toString(): string
    {
        return $this->raw;
    }
}
