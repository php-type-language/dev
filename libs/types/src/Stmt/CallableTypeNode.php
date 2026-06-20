<?php

declare(strict_types=1);

namespace TypeLang\Node\Stmt;

use TypeLang\Node\Name;
use TypeLang\Node\Stmt\Callable\CallableParametersListNode;

class CallableTypeNode extends TypeStatement
{
    public function __construct(
        public Name $name,
        public CallableParametersListNode $parameters = new CallableParametersListNode(),
        public ?TypeStatement $type = null,
    ) {}
}
