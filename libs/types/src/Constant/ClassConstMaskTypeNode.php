<?php

declare(strict_types=1);

namespace TypeLang\Type\Constant;

use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Name;
use TypeLang\Type\TypeNode;

/**
 * A class constant referenced by a name written in part.
 *
 * ```
 *  Some\Any::CONST_*
 *  ^^^^^^^^         the class the constant belongs to
 *            ^^^^^^^ the mask its name is written as
 * ```
 */
final class ClassConstMaskTypeNode extends TypeNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public Name $class,
        public MaskNode $mask = new MaskNode([
            new WildcardMaskNode(),
        ]),
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
