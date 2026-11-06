<?php

declare(strict_types=1);

namespace TypeLang\Type\Constant;

use TypeLang\Type\Identifier;
use TypeLang\Type\Name\TypeName;
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
        public TypeName $class,
        public Identifier $constant,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }

    /**
     * Creates a constant reference out of a class name and a constant name.
     *
     * @param int<0, max> $offset
     */
    public static function from(string|\Stringable $class, string|\Stringable $constant, int $offset = 0): self
    {
        return new self(TypeName::from($class, $offset), Identifier::from($constant, $offset), $offset);
    }
}
