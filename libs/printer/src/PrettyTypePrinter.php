<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Printer\NodePrinter\Callable\CallableParameterNodePrinter;
use TypeLang\Printer\NodePrinter\Callable\CallableTypeNodePrinter;
use TypeLang\Printer\NodePrinter\Condition\ConditionalTypeNodePrinter;
use TypeLang\Printer\NodePrinter\Condition\ConditionNodePrinter;
use TypeLang\Printer\NodePrinter\Constant\ClassConstMaskTypeNodePrinter;
use TypeLang\Printer\NodePrinter\Constant\ClassConstTypeNodePrinter;
use TypeLang\Printer\NodePrinter\Constant\ConstMaskTypeNodePrinter;
use TypeLang\Printer\NodePrinter\LiteralNodePrinter;
use TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter\IntersectionTypeNodePrinter;
use TypeLang\Printer\NodePrinter\LogicalTypeNodePrinter\UnionTypeNodePrinter;
use TypeLang\Printer\NodePrinter\NamedTypeNodePrinter;
use TypeLang\Printer\NodePrinter\NullableTypeNodePrinter;
use TypeLang\Printer\NodePrinter\Registry\CachedNodePrinterRegistry;
use TypeLang\Printer\NodePrinter\Registry\NodePrinterRegistry;
use TypeLang\Printer\NodePrinter\Shape\FieldNodePrinter\ComplexFieldNodePrinter;
use TypeLang\Printer\NodePrinter\Shape\FieldNodePrinter\ImplicitFieldNodePrinter;
use TypeLang\Printer\NodePrinter\Shape\FieldNodePrinter\NamedFieldNodePrinter;
use TypeLang\Printer\NodePrinter\Shape\FieldNodePrinter\ScalarFieldNodePrinter;
use TypeLang\Printer\NodePrinter\Template\TemplateBoundNodePrinter;
use TypeLang\Printer\NodePrinter\Template\TemplateListNodePrinter;
use TypeLang\Printer\NodePrinter\Template\TemplateParameterNodePrinter;
use TypeLang\Printer\NodePrinter\Template\TypedTemplateArgumentNodePrinter;
use TypeLang\Printer\NodePrinter\Template\WildcardTemplateArgumentNodePrinter;
use TypeLang\Printer\NodePrinter\ThisTypeNodePrinter;
use TypeLang\Printer\NodePrinter\TypeOffsetAccessTypeNodePrinter;
use TypeLang\Printer\NodePrinter\TypesListTypeNodePrinter;
use TypeLang\Printer\NodePrinter\VariableNodePrinter;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Condition\ConditionalTypeNode;
use TypeLang\Type\Condition\ConditionNode;
use TypeLang\Type\Constant\ClassConstMaskTypeNode;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Constant\ConstMaskTypeNode;
use TypeLang\Type\Literal\LiteralNode;
use TypeLang\Type\LogicalTypeNode\IntersectionTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\ComplexFieldNode;
use TypeLang\Type\Shape\ImplicitFieldNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Shape\ScalarFieldNode;
use TypeLang\Type\Template\TemplateBoundNode;
use TypeLang\Type\Template\TemplateListNode;
use TypeLang\Type\Template\TemplateParameterListNode;
use TypeLang\Type\Template\TemplateParameterNode;
use TypeLang\Type\Template\TypedTemplateArgumentNode;
use TypeLang\Type\Template\WildcardTemplateArgumentNode;
use TypeLang\Type\ThisTypeNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\TypeOffsetAccessTypeNode;
use TypeLang\Type\VariableNode;
use TypeLang\Type\WrappingTypeNode\NullableTypeNode;
use TypeLang\Type\WrappingTypeNode\TypesListTypeNode;

/**
 * Prints a type the way it is written.
 */
final class PrettyTypePrinter implements TypePrinterInterface
{
    private readonly TypePrinter $printer;

    public function __construct(
        PrinterOptions $options = new PrinterOptions(),
    ) {
        $this->printer = new TypePrinter(
            registry: new CachedNodePrinterRegistry(self::createRegistry()),
            options: $options,
        );
    }

    /**
     * Gets the printers of all the nodes, which a registry of another printer
     * may replace one by one.
     */
    public static function createRegistry(): NodePrinterRegistry
    {
        $template = new TemplateListNodePrinter();

        return new NodePrinterRegistry([
            LiteralNode::class => new LiteralNodePrinter(),
            NamedTypeNode::class => new NamedTypeNodePrinter(),
            ThisTypeNode::class => new ThisTypeNodePrinter(),
            VariableNode::class => new VariableNodePrinter(),
            CallableTypeNode::class => new CallableTypeNodePrinter(),
            CallableParameterNode::class => new CallableParameterNodePrinter(),
            ConditionalTypeNode::class => new ConditionalTypeNodePrinter(),
            ConditionNode::class => new ConditionNodePrinter(),
            UnionTypeNode::class => new UnionTypeNodePrinter(),
            IntersectionTypeNode::class => new IntersectionTypeNodePrinter(),
            NullableTypeNode::class => new NullableTypeNodePrinter(),
            TypesListTypeNode::class => new TypesListTypeNodePrinter(),
            TypeOffsetAccessTypeNode::class => new TypeOffsetAccessTypeNodePrinter(),
            ClassConstTypeNode::class => new ClassConstTypeNodePrinter(),
            ClassConstMaskTypeNode::class => new ClassConstMaskTypeNodePrinter(),
            ConstMaskTypeNode::class => new ConstMaskTypeNodePrinter(),
            NamedFieldNode::class => new NamedFieldNodePrinter(),
            ScalarFieldNode::class => new ScalarFieldNodePrinter(),
            ComplexFieldNode::class => new ComplexFieldNodePrinter(),
            ImplicitFieldNode::class => new ImplicitFieldNodePrinter(),
            TemplateListNode::class => $template,
            TemplateParameterListNode::class => $template,
            TypedTemplateArgumentNode::class => new TypedTemplateArgumentNodePrinter(),
            WildcardTemplateArgumentNode::class => new WildcardTemplateArgumentNodePrinter(),
            TemplateParameterNode::class => new TemplateParameterNodePrinter(),
            TemplateBoundNode::class => new TemplateBoundNodePrinter(),
        ]);
    }

    public function print(TypeNode $stmt): string
    {
        return $this->printer->print($stmt);
    }
}
