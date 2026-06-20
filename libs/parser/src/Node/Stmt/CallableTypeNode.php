<?php

declare(strict_types=1);

namespace TypeLang\Parser\Node\Stmt;

use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Node\Stmt\Callable\CallableParametersListNode;

class CallableTypeNode extends TypeStatement
{
    public function __construct(
        public Name $name,
        public CallableParametersListNode $parameters = new CallableParametersListNode(),
        public ?TypeStatement $type = null,
    ) {}
}
