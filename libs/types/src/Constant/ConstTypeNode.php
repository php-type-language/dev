<?php

declare(strict_types=1);

namespace TypeLang\Type\Constant;

use TypeLang\Type\Identifier;
use TypeLang\Type\Name;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\TypeNode;

/**
 * A single constant, referenced by its whole name.
 *
 * ```
 *  Some\Any::CONST_NAME
 *  ^^^^^^^^             the class the constant belongs to
 *            ^^^^^^^^^^ the name of the constant
 * ```
 *
 * A name written in part stands for a set of constants, see the
 * {@see ConstMaskTypeNode}.
 */
final class ConstTypeNode extends TypeNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        /**
         * The class or the namespace the constant belongs to.
         *
         * A constant of a namespace written by its whole name
         * (`Some\CONST_NAME`) is a name of a type in the source, so the
         * parser builds it as a {@see \TypeLang\Type\NamedTypeNode}.
         */
        public Name|NamespaceName $owner,
        public Identifier $constant,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }

    public function __clone()
    {
        $this->owner = clone $this->owner;
        $this->constant = clone $this->constant;
    }
}
