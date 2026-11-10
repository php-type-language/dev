<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * A node that stands for a piece of text: an identifier, a name or a variable.
 *
 * The text of a node is the one it is written with, so a node is told apart
 * from the others by {@see toString()} alone.
 */
abstract class SymbolNode extends Node implements \Stringable
{
    /**
     * Returns the text as it is written.
     *
     * @api
     *
     * @return non-empty-string
     */
    abstract public function toString(): string;

    /**
     * Returns the text as it is written, in lower case.
     *
     * @api
     *
     * @return non-empty-lowercase-string
     */
    public function toLowerString(): string
    {
        return \strtolower($this->toString());
    }

    /**
     * Whether the text is the name of a "virtual" type, which cannot be
     * defined in PHP.
     *
     * - `SomeClass`, `false` - not virtual.
     * - `non-empty-array`, `empty-string` - virtual.
     *
     * @api
     */
    public function isVirtual(): bool
    {
        return \str_contains($this->toString(), '-');
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
