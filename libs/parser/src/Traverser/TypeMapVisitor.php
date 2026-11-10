<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Constant\ClassConstMaskTypeNode;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;
use TypeLang\Type\Node;

/**
 * Replaces the names of types with the ones the given closure returns.
 */
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
        return ($this->transform)($name) ?? $name;
    }

    /**
     * A namespace of a mask is mapped as the name of its last word, a mask
     * written with none, from the root, or with no words is left as it is.
     */
    private function mapNamespace(?NamespaceName $namespace): ?NamespaceName
    {
        if ($namespace === null || $namespace->kind !== NamespaceKind::Qualified) {
            return $namespace;
        }

        $words = $namespace->toArray();
        $last = \array_pop($words);

        if ($last === null) {
            return $namespace;
        }

        $name = new Name(
            $last,
            $words === [] ? null : new NamespaceName(\implode(NamespaceName::DELIMITER, $words)),
            $namespace->offset,
        );

        $mapped = $this->map($name);

        return new NamespaceName($mapped->toString(), NamespaceKind::Qualified, $namespace->offset);
    }

    public function enter(Node $node): ?Command
    {
        switch (true) {
            case $node instanceof NamedTypeNode:
            case $node instanceof CallableTypeNode:
                $node->name = $this->map($node->name);

                return null;

            case $node instanceof ConstMaskTypeNode:
                $node->namespace = $this->mapNamespace($node->namespace);

                return null;

            case $node instanceof ClassConstTypeNode:
            case $node instanceof ClassConstMaskTypeNode:
                $node->class = $this->map($node->class);

                return null;

            default:
                return null;
        }
    }
}
