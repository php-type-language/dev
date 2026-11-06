<?php

declare(strict_types=1);

namespace TypeLang\Type\Name;

use TypeLang\Type\Identifier;
use TypeLang\Type\Name;

/**
 * A name of a type, which is made of at least one identifier.
 *
 * ```
 *  Some\Any<int>
 *  ^^^^^^^^      the name of a type
 * ```
 *
 * @template-extends Name<true>
 */
final class TypeName extends Name
{
    /**
     * The first of the {@see $parts}, which is the one an alias is resolved by.
     */
    public readonly Identifier $first;

    /**
     * The last of the {@see $parts}, which is the short name of a class.
     */
    public readonly Identifier $last;

    /**
     * @param iterable<array-key, Identifier> $parts
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of parts are empty
     */
    public function __construct(
        iterable $parts,
        bool $isFullyQualified = false,
        int $offset = 0,
    ) {
        $parts = self::normalizeParts($parts);

        if ($parts === []) {
            throw new \InvalidArgumentException('Name parts count can not be empty');
        }

        parent::__construct($parts, $isFullyQualified, $offset);

        $this->first = $parts[0];
        $this->last = $parts[\count($parts) - 1];
    }

    /**
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of the name has no identifiers
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
