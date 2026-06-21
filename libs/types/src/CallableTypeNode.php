<?php

declare(strict_types=1);

namespace TypeLang\Type;

use TypeLang\Type\Callable\CallableParametersListNode;

final class CallableTypeNode extends TypeNode
{
    public function __construct(
        public Name $name,
        public CallableParametersListNode $parameters = new CallableParametersListNode(),
        public ?TypeNode $type = null,
    ) {}
}
