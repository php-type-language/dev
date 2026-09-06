<?php

declare(strict_types=1);

namespace TypeLang\Type;

final class ClassConstMaskNode extends TypeNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public Name $class,
        public ?Identifier $constant = null,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
