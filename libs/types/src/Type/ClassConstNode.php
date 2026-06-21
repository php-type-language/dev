<?php

declare(strict_types=1);

namespace TypeLang\Node\Type;

use TypeLang\Node\Identifier;
use TypeLang\Node\Name;

class ClassConstNode extends TypeNode
{
    public function __construct(
        public Name $class,
        public Identifier $constant,
    ) {}
}
