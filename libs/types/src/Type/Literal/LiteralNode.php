<?php

declare(strict_types=1);

namespace TypeLang\Node\Type\Literal;

use TypeLang\Node\Type\TypeNode;

/**
 * @template TValue of mixed = mixed
 * @template-implements LiteralNodeInterface<TValue>
 */
abstract class LiteralNode extends TypeNode implements LiteralNodeInterface
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
