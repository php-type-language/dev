<?php

declare(strict_types=1);

namespace TypeLang\Type;

final class ConstMaskNode extends TypeNode
{
    /**
     * @param int<0, max> $offset
     */
    public function __construct(
        public Name $name,
        int $offset = 0,
    ) {
        parent::__construct($offset);
    }
}
