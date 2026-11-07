<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Printer\NodePrinter\Registry\NodePrinterRegistry;
use TypeLang\Printer\NodePrinter\Registry\CachedNodePrinterRegistry;
use TypeLang\Printer\NativeTypePrinter\TypeAliases;
use TypeLang\Printer\NodePrinter\Callable\NativeCallableTypeNodePrinter;
use TypeLang\Printer\NodePrinter\Condition\NativeConditionalTypeNodePrinter;
use TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter\NativeIntersectionTypeNodePrinter;
use TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter\NativeUnionTypeNodePrinter;
use TypeLang\Printer\NodePrinter\NativeFixedNodePrinter;
use TypeLang\Printer\NodePrinter\NativeLiteralNodePrinter;
use TypeLang\Printer\NodePrinter\NativeNamedTypeNodePrinter;
use TypeLang\Printer\NodePrinter\NativeNullableTypeNodePrinter;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Condition\ConditionalTypeNode;
use TypeLang\Type\Constant\ClassConstMaskTypeNode;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Literal\LiteralNode;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;
use TypeLang\Type\WrappingTypeNode\TypeOffsetAccessTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;
use TypeLang\TypeSystem\Registry;

/**
 * Prints the native PHP type a type stands for.
 */
final class NativeTypePrinter implements TypePrinterInterface
{
    private readonly TypeAliases $aliases;

    private readonly TypePrinter $printer;

    /**
     * @param iterable<non-empty-string, non-empty-string> $aliases
     * @param Registry|null $registry the names of the types the printer knows, all of the
     *        dialects by default
     */
    public function __construct(
        iterable $aliases = [],
        PrinterOptions $options = new PrinterOptions(),
        ?Registry $registry = null,
    ) {
        $this->aliases = new TypeAliases($registry);

        foreach ($aliases as $alias => $type) {
            $this->aliases->add($alias, $type);
        }

        $this->printer = new TypePrinter(
            registry: new CachedNodePrinterRegistry($this->createRegistry()),
            options: $options,
        );
    }

    private function createRegistry(): NodePrinterRegistry
    {
        return PrettyTypePrinter::createRegistry()
            ->with(LiteralNode::class, new NativeLiteralNodePrinter())
            ->with(NamedTypeNode::class, new NativeNamedTypeNodePrinter($this->aliases))
            ->with(CallableTypeNode::class, new NativeCallableTypeNodePrinter($this->aliases))
            ->with(UnionTypeNode::class, new NativeUnionTypeNodePrinter())
            ->with(IntersectionTypeNode::class, new NativeIntersectionTypeNodePrinter())
            ->with(ConditionalTypeNode::class, new NativeConditionalTypeNodePrinter())
            ->with(NullableTypeNode::class, new NativeNullableTypeNodePrinter())
            ->with(ThisTypeNode::class, new NativeFixedNodePrinter('self'))
            ->with(TypesListTypeNode::class, new NativeFixedNodePrinter('iterable'))
            ->with(TypeOffsetAccessTypeNode::class, new NativeFixedNodePrinter('mixed'))
            ->with(ClassConstTypeNode::class, new NativeFixedNodePrinter('mixed'))
            ->with(ClassConstMaskTypeNode::class, new NativeFixedNodePrinter('mixed'))
            ->with(ConstMaskTypeNode::class, new NativeFixedNodePrinter('mixed'));
    }

    /**
     * @api
     *
     * @param non-empty-string $alias
     * @param non-empty-string $type
     */
    public function addTypeAlias(string $alias, string $type): void
    {
        $this->aliases->add($alias, $type);
    }

    /**
     * @api
     *
     * @param non-empty-string $alias
     * @param non-empty-list<non-empty-string> $types
     */
    public function addUnionTypeAlias(string $alias, array $types): void
    {
        $this->aliases->addUnion($alias, $types);
    }

    /**
     * @api
     *
     * @param non-empty-string $alias
     * @param non-empty-list<non-empty-string> $types
     */
    public function addIntersectionTypeAlias(string $alias, array $types): void
    {
        $this->aliases->addIntersection($alias, $types);
    }

    public function print(TypeNode $stmt): string
    {
        return $this->printer->print($stmt);
    }
}
