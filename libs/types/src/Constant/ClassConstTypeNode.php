<?php

declare(strict_types=1);

namespace TypeLang\Type\Constant;

use TypeLang\Type\Name;
use TypeLang\Type\TypeNode;

/**
 * A constant of a class, referenced by its whole name.
 *
 * ```
 *  Some\Any::CONST_NAME
 *  ^^^^^^^^             the class the constant belongs to
 *            ^^^^^^^^^^ the name of the constant
 * ```
 *
 * A name written in part is a {@see ClassConstMaskTypeNode} instead.
 */
final class ClassConstTypeNode extends TypeNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public Name $class,
        public Name $constant,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }

    /**
     * Creates a constant reference out of a class name and a constant name.
     *
     * @param non-empty-string $class
     * @param non-empty-string $constant
     * @param int<0, max> $offset
     */
    public static function from(string $class, string $constant, int $offset = 0): self
    {
        return new self(new Name($class, $offset), new Name($constant, $offset), $offset);
    }
}
