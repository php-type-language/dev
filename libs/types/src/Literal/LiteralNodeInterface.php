<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * A value standing where a type is expected.
 *
 * @template-covariant TValue of mixed = mixed
 *
 * @property-read TValue $value Gets a PHP representation of the literal value.
 * @property-read string $raw Gets the original literal value specified in the token.
 */
interface LiteralNodeInterface extends \Stringable
{
    /**
     * Returns the processed ({@see $value}) literal value as a string.
     */
    public function __toString(): string;
}
