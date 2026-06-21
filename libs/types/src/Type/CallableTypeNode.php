<?php

declare(strict_types=1);

namespace TypeLang\Node\Type;

use TypeLang\Node\Name;
use TypeLang\Node\Type\Callable\CallableParametersListNode;

class CallableTypeNode extends TypeNode
{
    public function __construct(
        public Name $name,
        public CallableParametersListNode $parameters = new CallableParametersListNode(),
        public ?TypeNode $type = null,
    ) {}
}
