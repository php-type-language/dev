<?php

declare(strict_types=1);

namespace TypeLang\Node\Stmt;

use TypeLang\Node\Identifier;
use TypeLang\Node\Name;

class ClassConstMaskNode extends TypeStatement
{
    public function __construct(
        public Name $class,
        public Identifier|null $constant = null,
    ) {}
}
