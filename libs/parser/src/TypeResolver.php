<?php

declare(strict_types=1);

namespace TypeLang\Parser;

use TypeLang\Node\Type\TypeNode;
use TypeLang\Parser\Traverser\TypeMapVisitor;

final class TypeResolver implements TypeResolverInterface
{
    public function resolve(TypeNode $type, callable $transform): TypeNode
    {
        Traverser::through(new TypeMapVisitor($transform(...)), [$type]);

        return $type;
    }
}
