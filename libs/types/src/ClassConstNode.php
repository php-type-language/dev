<?php

declare(strict_types=1);

namespace TypeLang\Type;

final class ClassConstNode extends TypeNode
{
    public function __construct(
        public Name $class,
        public Identifier $constant,
    ) {}
}
