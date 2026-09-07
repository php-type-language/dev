<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Printer\Exception\NonPrintableNodeException;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\CallableTypeNode;
use TypeLang\Type\ClassConstMaskNode;
use TypeLang\Type\ClassConstNode;
use TypeLang\Type\Condition\Condition;
use TypeLang\Type\Condition\EqualConditionNode;
use TypeLang\Type\Condition\NotEqualConditionNode;
use TypeLang\Type\ConstMaskNode;
use TypeLang\Type\IntersectionTypeNode;
use TypeLang\Type\Literal\LiteralNode;
use TypeLang\Type\LogicalTypeNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\NullableTypeNode;
use TypeLang\Type\Shape\ComplexFieldNode;
use TypeLang\Type\Shape\FieldNode;
use TypeLang\Type\Shape\FieldsListNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Shape\ScalarFieldNode;
use TypeLang\Type\Template\TemplateArgumentListNode;
use TypeLang\Type\Template\TemplateArgumentNode;
use TypeLang\Type\Template\TemplateBoundEdgeNode;
use TypeLang\Type\Template\TemplateParameterListNode;
use TypeLang\Type\Template\TemplateParameterNode;
use TypeLang\Type\TernaryExpressionNode;
use TypeLang\Type\ThisNode;
use TypeLang\Type\TypeNode;
use TypeLang\Type\TypeOffsetAccessNode;
use TypeLang\Type\TypesListNode;
use TypeLang\Type\UnionTypeNode;
use TypeLang\Type\VariableNode;
use TypeLang\Type\WildcardNode;

class PrettyTypePrinter extends TypePrinter
{
    public const DEFAULT_WRAP_INTERSECTION_TYPE = true;

    public const DEFAULT_WRAP_UNION_TYPE = false;

    public const DEFAULT_WRAP_CALLABLE_RETURN_TYPE = true;

    /**
     * @var int<0, max>
     */
    public const DEFAULT_MULTILINE_SHAPE = 1;

    public function __construct(
        string $newLine = self::DEFAULT_NEW_LINE_DELIMITER,
        string $indention = self::DEFAULT_INDENTION,
        /**
         * Wrap union type (joined by "|") by whitespaces.
         *
         * ```
         * $wrapUnionType = true;
         * // Type | Some | Any
         *
         * $wrapUnionType = false;
         * // Type|Some|Any
         * ```
         */
        public readonly bool $wrapUnionType = self::DEFAULT_WRAP_UNION_TYPE,
        /**
         * Wrap intersection type (joined by "&") by whitespaces.
         *
         * ```
         *  $wrapIntersectionType = true;
         *  // Type & Some & Any
         *
         *  $wrapIntersectionType = false;
         *  // Type&Some&Any
         *  ```
         */
        public readonly bool $wrapIntersectionType = self::DEFAULT_WRAP_INTERSECTION_TYPE,
        /**
         * Add whitespace at the start of callable return type.
         *
         * ```
         * $wrapCallableReturnType = true;
         * // callable(): void
         *
         * $wrapCallableReturnType = false;
         * // callable():void
         * ```
         */
        public readonly bool $wrapCallableReturnType = self::DEFAULT_WRAP_CALLABLE_RETURN_TYPE,
        /**
         * The number of elements in the shape after which it is
         * formatted as multiline.
         *
         * ```
         * $multilineShape = 2;
         * // array{some, any}
         *
         * $multilineShape = 1;
         * // array{
         * //     some,
         * //     any
         * // }
         * ```
         *
         * @var int<0, max>
         */
        public readonly int $multilineShape = self::DEFAULT_MULTILINE_SHAPE,
    ) {
        parent::__construct($newLine, $indention);
    }

    /**
     * @throws NonPrintableNodeException
     */
    protected function make(TypeNode $stmt): string
    {
        return match (true) {
            $stmt instanceof LiteralNode => $this->printLiteralNode($stmt),
            $stmt instanceof NamedTypeNode => $this->printNamedTypeNode($stmt),
            $stmt instanceof ClassConstNode => $this->printClassConstNode($stmt),
            $stmt instanceof ClassConstMaskNode => $this->printClassConstMaskNode($stmt),
            $stmt instanceof ConstMaskNode => $this->printConstMaskNode($stmt),
            $stmt instanceof CallableTypeNode => $this->printCallableTypeNode($stmt),
            $stmt instanceof UnionTypeNode => $this->printUnionTypeNode($stmt),
            $stmt instanceof IntersectionTypeNode => $this->printIntersectionTypeNode($stmt),
            $stmt instanceof NullableTypeNode => $this->printNullableType($stmt),
            $stmt instanceof TernaryExpressionNode => $this->printTernaryType($stmt),
            $stmt instanceof TypesListNode => $this->printTypeListNode($stmt),
            $stmt instanceof TypeOffsetAccessNode => $this->printTypeOffsetAccessNode($stmt),
            $stmt instanceof WildcardNode => $this->printWildcardNode($stmt),
            $stmt instanceof ThisNode => $this->printThisNode($stmt),
            default => throw NonPrintableNodeException::becauseInvalidNodeGiven($stmt),
        };
    }

    /**
     * @param LiteralNode<mixed> $node
     */
    protected function printLiteralNode(LiteralNode $node): string
    {
        return $node->raw;
    }

    /**
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printNamedTypeNode(NamedTypeNode $node): string
    {
        $result = $node->name->toString();

        if ($node->fields !== null) {
            $result .= $this->printShapeFieldsNode($node, $node->fields);
        } elseif ($node->arguments !== null) {
            $result .= $this->printTemplateArgumentsNode($node->arguments);
        }

        /** @var non-empty-string */
        return $result;
    }

    /**
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printShapeFieldsNode(NamedTypeNode $node, FieldsListNode $shape): string
    {
        if (\count($shape->items) <= $this->multilineShape) {
            return \vsprintf('{%s}', [
                \implode(', ', $this->getShapeFieldsNodes($node, $shape, false)),
            ]);
        }

        return \vsprintf('{%s%s%s}', [
            $this->newLine,
            \implode(',' . $this->newLine, $this->nested(section: fn(): array
                => $this->getShapeFieldsNodes($node, $shape, true))),
            $this->newLine . $this->prefix(),
        ]);
    }

    /**
     * @return list<non-empty-string>
     * @throws NonPrintableNodeException
     */
    private function getShapeFieldsNodes(NamedTypeNode $node, FieldsListNode $shape, bool $multiline): array
    {
        $prefix = $this->prefix();

        $fields = [];

        foreach ($shape->items as $field) {
            $fields[] = $prefix . $this->printShapeFieldNode($field);
        }

        if (!$shape->isSealed || $node->arguments !== null) {
            $prefix .= '...';

            if ($node->arguments !== null) {
                $prefix .= $this->printTemplateArgumentsNode($node->arguments);
            }

            $fields[] = $prefix;
        }

        /** @var list<non-empty-string> */
        return $fields;
    }

    /**
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printShapeFieldNode(FieldNode $field): string
    {
        $name = $this->printShapeFieldName($field);

        if ($name !== '') {
            if ($field->isOptional) {
                $name .= '?';
            }

            return \vsprintf('%s: %s', [
                $name,
                $this->make($field->type),
            ]);
        }

        /** @var non-empty-string */
        return $this->make($field->type);
    }

    /**
     * @throws NonPrintableNodeException
     */
    protected function printShapeFieldName(FieldNode $field): string
    {
        return match (true) {
            $field instanceof NamedFieldNode => $this->printNamedShapeFieldName($field),
            $field instanceof ScalarFieldNode => $this->printScalarShapeFieldName($field),
            $field instanceof ComplexFieldNode => $this->printComplexShapeFieldName($field),
            default => $this->printUnknownShapeFieldName($field),
        };
    }

    protected function printNamedShapeFieldName(NamedFieldNode $field): string
    {
        return $field->key->toString();
    }

    /**
     * A scalar key is written back the way it was written, so that a "0x2A"
     * does not come out as a "42".
     */
    protected function printScalarShapeFieldName(ScalarFieldNode $field): string
    {
        return $field->key->raw;
    }

    /**
     * @throws NonPrintableNodeException
     */
    protected function printComplexShapeFieldName(ComplexFieldNode $field): string
    {
        return $this->make($field->key);
    }

    protected function printUnknownShapeFieldName(FieldNode $field): string
    {
        return '';
    }

    /**
     * @param TemplateArgumentListNode<TemplateArgumentNode>|TemplateArgumentListNode $arguments
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printTemplateArgumentsNode(TemplateArgumentListNode $arguments): string
    {
        $result = [];

        foreach ($arguments as $argument) {
            $result[] = $this->printTemplateArgumentNode($argument);
        }

        return \sprintf('<%s>', \implode(', ', $result));
    }

    /**
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printTemplateArgumentNode(TemplateArgumentNode $argument): string
    {
        /** @var non-empty-string $result */
        $result = $this->make($argument->value);

        if ($argument->hint !== null) {
            return $argument->hint->toString() . ' ' . $result;
        }

        return $result;
    }

    /**
     * @param TemplateParameterListNode<TemplateParameterNode>|TemplateParameterListNode $parameters
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printTemplateParametersNode(TemplateParameterListNode $parameters): string
    {
        $result = [];

        foreach ($parameters as $parameter) {
            $result[] = $this->printTemplateParameterNode($parameter);
        }

        return \sprintf('<%s>', \implode(', ', $result));
    }

    /**
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printTemplateParameterNode(TemplateParameterNode $parameter): string
    {
        $result = $parameter->name->toString();

        if ($parameter->upper !== null) {
            $result .= ' ' . $this->printTemplateBoundEdgeNode($parameter->upper);
        }

        if ($parameter->lower !== null) {
            $result .= ' ' . $this->printTemplateBoundEdgeNode($parameter->lower);
        }

        if ($parameter->default !== null) {
            $result .= ' = ' . $this->make($parameter->default);
        }

        return $result;
    }

    /**
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printTemplateBoundEdgeNode(TemplateBoundEdgeNode $edge): string
    {
        return \sprintf('%s %s', $edge->operator->toString(), $this->make($edge->type));
    }

    /**
     * @return non-empty-string
     */
    protected function printClassConstNode(ClassConstNode $node): string
    {
        return \vsprintf('%s::%s', [
            $node->class->toString(),
            $node->constant->toString(),
        ]);
    }

    /**
     * @return non-empty-string
     */
    protected function printClassConstMaskNode(ClassConstMaskNode $node): string
    {
        return \vsprintf('%s::%s', [
            $node->class->toString(),
            $node->mask->toString(),
        ]);
    }

    /**
     * @return non-empty-string
     */
    protected function printConstMaskNode(ConstMaskNode $node): string
    {
        $result = $node->mask->toString();

        if ($node->namespace !== null) {
            $result = $node->namespace->toUnqualifiedString() . '\\' . $result;
        }

        if ($node->isFullyQualified) {
            return '\\' . $result;
        }

        return $result;
    }

    /**
     * @return non-empty-string
     */
    protected function printThisNode(ThisNode $node): string
    {
        return '$this';
    }

    /**
     * @return non-empty-string
     */
    protected function printVariableNode(VariableNode $node): string
    {
        return '$' . $node->name->toString();
    }

    /**
     * @return non-empty-string
     */
    protected function printWildcardNode(WildcardNode $node): string
    {
        return $node->toString();
    }

    /**
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printCallableTypeNode(CallableTypeNode $node): string
    {
        $result = $node->name->toString();

        // Add template parameters
        if ($node->templates !== null) {
            $result .= $this->printTemplateParametersNode($node->templates);
        }

        $arguments = [];

        foreach ($node->parameters as $argument) {
            $arguments[] = \rtrim($this->printCallableArgumentNode($argument));
        }

        // Add arguments
        $result .= \sprintf('(%s)', \implode(', ', $arguments));

        // Add return type
        if ($node->type !== null) {
            $returnType = $this->make($node->type);

            if ($this->shouldWrapReturnType($node->type)) {
                $returnType = \sprintf('(%s)', $returnType);
            }

            $returnTypeFormat = $this->wrapCallableReturnType ? ': %s' : ':%s';
            $result .= \sprintf($returnTypeFormat, $returnType);
        }

        return $result;
    }

    /**
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printCallableArgumentNode(CallableParameterNode $node): string
    {
        $result = 'mixed';

        if ($node->type !== null) {
            /** @var non-empty-string $result */
            $result = $this->make($node->type);
        }

        if ($node->name !== null) {
            $result .= ' ';
        }

        if ($node->isOutput) {
            $result .= '&';
        }

        if ($node->isVariadic) {
            $result .= '...';
        }

        if ($node->name !== null) {
            $result .= $this->printVariableNode($node->name);
        }

        if ($node->isOptional) {
            $result .= '=';
        }

        return $result;
    }

    protected function shouldWrapReturnType(TypeNode $type): bool
    {
        if ($type instanceof LogicalTypeNode) {
            return true;
        }

        return self::containsLogicalType($type);
    }

    /**
     * Searches for a {@see LogicalTypeNode} in depth: The search is stopped
     * as soon as a type nesting its children into template arguments or
     * shape fields is reached.
     */
    private static function containsLogicalType(TypeNode $type): bool
    {
        $stack = [$type];

        while ($stack !== []) {
            $node = \array_pop($stack);

            if ($node instanceof LogicalTypeNode) {
                return true;
            }

            if (self::shouldStopSearch($node)) {
                return false;
            }

            $children = self::fetchChildNodes($node);

            for ($index = \count($children) - 1; $index >= 0; --$index) {
                $stack[] = $children[$index];
            }
        }

        return false;
    }

    private static function shouldStopSearch(Node $node): bool
    {
        if (!$node instanceof NamedTypeNode) {
            return false;
        }

        // Stop on non-empty template parameters.
        $isInTemplate = $node->arguments !== null
            && $node->arguments->items !== [];

        // Stop on non-empty shape fields.
        $isInShape = $node->fields !== null
            && $node->fields->items !== [];

        return $isInTemplate || $isInShape;
    }

    /**
     * @return list<Node>
     */
    private static function fetchChildNodes(Node $node): array
    {
        $result = [];

        foreach (\get_object_vars($node) as $value) {
            if ($value instanceof Node) {
                $result[] = $value;

                continue;
            }

            if (!\is_iterable($value)) {
                continue;
            }

            foreach ($value as $child) {
                if (!$child instanceof Node) {
                    break;
                }

                $result[] = $child;
            }
        }

        return $result;
    }

    /**
     * @param UnionTypeNode<TypeNode> $node
     * @return non-empty-string
     */
    protected function printUnionTypeNode(UnionTypeNode $node): string
    {
        $delimiter = $this->wrapUnionType ? ' | ' : '|';

        /** @var non-empty-string */
        return \vsprintf($this->nesting++ > 0 ? '(%s)' : '%s', [
            \implode($delimiter, [
                ...$this->unwrapAndPrint($node),
            ]),
        ]);
    }

    /**
     * @param IntersectionTypeNode<TypeNode> $node
     * @return non-empty-string
     */
    protected function printIntersectionTypeNode(IntersectionTypeNode $node): string
    {
        $delimiter = $this->wrapIntersectionType ? ' & ' : '&';

        /** @var non-empty-string */
        return \vsprintf($this->nesting++ > 0 ? '(%s)' : '%s', [
            \implode($delimiter, [
                ...$this->unwrapAndPrint($node),
            ]),
        ]);
    }

    /**
     * @param NullableTypeNode<TypeNode> $node
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printNullableType(NullableTypeNode $node): string
    {
        return '?' . $this->make($node->type);
    }

    /**
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printTernaryType(TernaryExpressionNode $node): string
    {
        return \vsprintf('(%s %s %s ? %s : %s)', [
            $this->printConditionOperand($node->condition->subject),
            $this->printCondition($node->condition),
            $this->printConditionOperand($node->condition->target),
            $this->make($node->then),
            $this->make($node->else),
        ]);
    }

    /**
     * @throws NonPrintableNodeException
     */
    protected function printConditionOperand(TypeNode|VariableNode $node): string
    {
        if ($node instanceof VariableNode) {
            return $this->printVariableNode($node);
        }

        return $this->make($node);
    }

    /**
     * @return non-empty-string
     */
    protected function printCondition(Condition $node): string
    {
        return match (true) {
            $node instanceof EqualConditionNode => 'is',
            $node instanceof NotEqualConditionNode => 'is not',
            default => throw NonPrintableNodeException::becauseInvalidNodeGiven($node),
        };
    }

    /**
     * @param TypesListNode<TypeNode> $node
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printTypeListNode(TypesListNode $node): string
    {
        $result = $this->make($node->type);

        return $result . '[]';
    }

    /**
     * @param TypeOffsetAccessNode<TypeNode> $node
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printTypeOffsetAccessNode(TypeOffsetAccessNode $node): string
    {
        $result = $this->make($node->type);

        return $result . '[' . $this->make($node->access) . ']';
    }
}
