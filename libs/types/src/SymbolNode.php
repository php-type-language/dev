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
     * Words of a class that are not names of classes but stand for one.
     *
     * @var non-empty-array<non-empty-lowercase-string, true>
     */
    private const SPECIAL_CLASS_NAMES = [
        'self' => true,
        'parent' => true,
        'static' => true,
    ];

    /**
     * Words that PHP reserves for the types it has built in, so no class can
     * be named by one of them.
     *
     * @var non-empty-array<non-empty-lowercase-string, true>
     */
    private const BUILTIN_TYPE_NAMES = [
        'mixed' => true,
        'string' => true,
        'int' => true,
        'float' => true,
        'bool' => true,
        'object' => true,
        'array' => true,
        'void' => true,
        'never' => true,
        'callable' => true,
        'iterable' => true,
        'null' => true,
        'true' => true,
        'false' => true,
    ];

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
     * Whether the text is one of `self`, `parent` and `static`.
     *
     * @api
     */
    public function isSpecial(): bool
    {
        return isset(self::SPECIAL_CLASS_NAMES[$this->toLowerString()]);
    }

    /**
     * Whether the text is the name of a type that PHP has built in, such as
     * `int`, `array` or `callable`.
     *
     * @api
     */
    public function isBuiltin(): bool
    {
        return isset(self::BUILTIN_TYPE_NAMES[$this->toLowerString()]);
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
