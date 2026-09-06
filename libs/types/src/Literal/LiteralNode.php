<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

use TypeLang\Type\TypeNode;

/**
 * @template TValue of mixed = mixed
 *
 * @template-implements LiteralNodeInterface<TValue>
 */
abstract class LiteralNode extends TypeNode implements LiteralNodeInterface
{
    public function __construct(
        /**
         * An alias of {@see value()} method.
         *
         * @var TValue
         */
        public readonly mixed $value,
        /**
         * An alias of {@see raw()} method.
         */
        public readonly string $raw,
    ) {}

    /**
     * @return TValue
     */
    public function value(): mixed
    {
        return $this->value;
    }

    public function raw(): string
    {
        return $this->raw;
    }

    public function __toString(): string
    {
        return $this->raw;
    }
}
