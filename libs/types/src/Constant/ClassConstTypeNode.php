<?php

declare(strict_types=1);

namespace TypeLang\Type\Constant;

use TypeLang\Type\Identifier;
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
        public Identifier $constant,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }

    public function __clone()
    {
        $this->class = clone $this->class;
        $this->constant = clone $this->constant;
    }
}
