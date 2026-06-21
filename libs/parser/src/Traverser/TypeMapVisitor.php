<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Node\Name;
use TypeLang\Node\Node;
use TypeLang\Node\Type\CallableTypeNode;
use TypeLang\Node\Type\ClassConstMaskNode;
use TypeLang\Node\Type\ClassConstNode;
use TypeLang\Node\Type\ConstMaskNode;
use TypeLang\Node\Type\NamedTypeNode;

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

            case $node instanceof ClassConstNode:
            case $node instanceof ClassConstMaskNode:
                $node->class = $this->map($node->class);

                return null;

            default:
                return null;
        }
    }
}
