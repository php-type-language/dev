<?php

declare(strict_types=1);

namespace TypeLang\Type\Stmt;

use TypeLang\Type\Name;
use TypeLang\Type\Stmt\Callable\CallableParametersListNode;

class CallableTypeNode extends TypeStatement
{
    public function __construct(
        public Name $name,
        public CallableParametersListNode $parameters = new CallableParametersListNode(),
        public ?TypeStatement $type = null,
    ) {}
}
