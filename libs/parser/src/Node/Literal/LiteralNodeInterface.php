<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Literal;

/**
 * @template-covariant TValue of mixed = mixed
 */
interface LiteralNodeInterface extends \Stringable
{
    /**
     * Gets a PHP representation of the literal value.
     *
     * @var TValue
     */
    public mixed $value {
        get;
    }

    /**
     * Gets the original literal value specified in the token.
     */
    public string $raw {
        get;
    }

    /**
     * Returns the processed ({@see $value}) literal value as a string.
     */
    public function __toString(): string;
}
