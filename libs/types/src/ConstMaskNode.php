<?php

declare(strict_types=1);

namespace TypeLang\Type;

final class ConstMaskNode extends TypeNode
{
    public function __construct(
        public Name $name,
    ) {}
}
