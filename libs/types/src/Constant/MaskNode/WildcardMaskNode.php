<?php

declare(strict_types=1);

namespace TypeLang\Type\Constant\MaskNode;

use TypeLang\Type\Node;

/**
 * An asterisk standing in the place of any run of characters of a constant name.
 *
 * ```
 *  Some\Any::CONST_*
 *  //              ^ the part of the name that is left unsaid
 * ```
 */
final class WildcardMaskNode extends Node implements \Stringable
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
