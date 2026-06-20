<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Type\Name;
use TypeLang\Type\Node;
use TypeLang\Type\Stmt\CallableTypeNode;
use TypeLang\Type\Stmt\ClassConstMaskNode;
use TypeLang\Type\Stmt\ConstMaskNode;
use TypeLang\Type\Stmt\NamedTypeNode;

final class TypeMapVisitor extends Visitor
{
    /**
     * @param \Closure(Name):(Name|null) $transform
     */
    public function __construct(
        private readonly \Closure $transform,
    ) {}

    private function map(Name $name): Name
    {
        $result = ($this->transform)($name);

        if ($result instanceof Name) {
            return $result;
        }

        return $name;
    }

    public function enter(Node $node): ?Command
    {
        switch (true) {
            case $node instanceof NamedTypeNode:
            case $node instanceof CallableTypeNode:
            case $node instanceof ConstMaskNode:
                $node->name = $this->map($node->name);

                return null;

            case $node instanceof ClassConstMaskNode:
                $node->class = $this->map($node->class);

                return null;

            default:
                return null;
        }
    }
}
