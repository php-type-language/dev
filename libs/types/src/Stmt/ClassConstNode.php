<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt;

use TypeLang\Type\Identifier;
use TypeLang\Type\Name;

class ClassConstNode extends TypeStatement
{
    public function __construct(
        public Name $class,
        public Identifier $constant,
    ) {}
}
