<?php

declare(strict_types=1);

namespace TypeLang\Type;

/**
 * A class constant referenced by a name written in part.
 *
 * ```
 *  Some\Any::CONST_*
 *  ^^^^^^^^         the class the constant belongs to
 *            ^^^^^^^ the mask its name is written as
 * ```
 */
final class ClassConstMaskNode extends TypeNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public Name $class,
        public MaskNode $mask = new MaskNode([new WildcardNode()]),
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
