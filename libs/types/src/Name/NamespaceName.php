<?php

declare(strict_types=1);

namespace TypeLang\Type\Name;

use TypeLang\Type\Name;

/**
 * A namespace, which may be written with no identifiers at all.
 *
 * ```
 *  Some\Any\SOME_*
 *  ^^^^^^^^        the namespace
 *
 *  \JSON_*
 *  ^               the root namespace, which is empty and fully qualified
 * ```
 *
 * @template-extends Name<false>
 */
final class NamespaceName extends Name
{
    /**
     * @param int<0, max> $offset
     */
    public static function from(string|\Stringable $name, int $offset = 0): self
    {
        if ($name instanceof self && $offset === $name->offset) {
            return $name;
        }

        $name = (string) $name;

        return new self(self::parseParts($name), self::parseFullyQualified($name), $offset);
    }
}
