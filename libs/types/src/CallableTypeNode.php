<?php

declare(strict_types=1);

namespace TypeLang\Type;

use TypeLang\Type\Callable\CallableParameterListNode;

final class CallableTypeNode extends TypeNode
{
    public function __construct(
        public Name $name,
        public CallableParameterListNode $parameters = new CallableParameterListNode(),
        public ?TypeNode $type = null,
    ) {}
}
