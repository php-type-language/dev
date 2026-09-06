<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * @template-covariant TValue of mixed = mixed
 */
interface LiteralNodeInterface extends \Stringable
{
    /**
     * Gets a PHP representation of the literal value.
     *
     * @return TValue
     */
    public function value(): mixed;

    /**
     * Gets the original literal value specified in the token.
     */
    public function raw(): string;

    /**
     * Returns the processed ({@see value()}) literal value as a string.
     */
    public function __toString(): string;
}
