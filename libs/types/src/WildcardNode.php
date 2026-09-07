<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * An asterisk standing in the place of something that is left unsaid.
 *
 * ```
 *  Some\Any<*>
 *  //       ^ an unspecified template argument
 *
 *  Some\Any::CONST_*
 *  //              ^ an unspecified part of a constant name
 * ```
 */
final class WildcardNode extends TypeNode implements \Stringable
{
    /**
     * @var non-empty-string
     */
    public const CHAR = '*';

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return self::CHAR;
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return self::CHAR;
    }
}
