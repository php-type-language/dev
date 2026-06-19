<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Parser\Node\Literal\LiteralNode;
use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Statement;
use TypeLang\Parser\Node\Stmt\Attribute\AttributeGroupNode;
use TypeLang\Parser\Node\Stmt\Attribute\AttributeGroupsListNode;
use TypeLang\Parser\Node\Stmt\Callable\ParameterNode;
use TypeLang\Parser\Node\Stmt\CallableTypeNode;
use TypeLang\Parser\Node\Stmt\ClassConstMaskNode;
use TypeLang\Parser\Node\Stmt\ClassConstNode;
use TypeLang\Parser\Node\Stmt\Condition\Condition;
use TypeLang\Parser\Node\Stmt\Condition\EqualConditionNode;
use TypeLang\Parser\Node\Stmt\Condition\GreaterOrEqualThanConditionNode;
use TypeLang\Parser\Node\Stmt\Condition\GreaterThanConditionNode;
use TypeLang\Parser\Node\Stmt\Condition\LessOrEqualThanConditionNode;
use TypeLang\Parser\Node\Stmt\Condition\LessThanConditionNode;
use TypeLang\Parser\Node\Stmt\Condition\NotEqualConditionNode;
use TypeLang\Parser\Node\Stmt\ConstMaskNode;
use TypeLang\Parser\Node\Stmt\IntersectionTypeNode;
use TypeLang\Parser\Node\Stmt\LogicalTypeNode;
use TypeLang\Parser\Node\Stmt\NamedTypeNode;
use TypeLang\Parser\Node\Stmt\NullableTypeNode;
use TypeLang\Parser\Node\Stmt\Shape\ClassConstMaskFieldNode;
use TypeLang\Parser\Node\Stmt\Shape\ConstMaskFieldNode;
use TypeLang\Parser\Node\Stmt\Shape\FieldNode;
use TypeLang\Parser\Node\Stmt\Shape\FieldsListNode;
use TypeLang\Parser\Node\Stmt\Shape\NamedFieldNode;
use TypeLang\Parser\Node\Stmt\Shape\NumericFieldNode;
use TypeLang\Parser\Node\Stmt\Shape\StringNamedFieldNode;
use TypeLang\Parser\Node\Stmt\Template\ArgumentNode;
use TypeLang\Parser\Node\Stmt\Template\ArgumentsListNode;
use TypeLang\Parser\Node\Stmt\Template\TemplateArgumentsListNode;
use TypeLang\Parser\Node\Stmt\TernaryConditionNode;
use TypeLang\Parser\Node\Stmt\TypeOffsetAccessNode;
use TypeLang\Parser\Node\Stmt\TypesListNode;
use TypeLang\Parser\Node\Stmt\TypeStatement;
use TypeLang\Parser\Node\Stmt\UnionTypeNode;
use TypeLang\Parser\Traverser;
use TypeLang\Printer\Exception\NonPrintableNodeException;

class PrettyPrinter extends Printer
{
    /**
     * @var bool
     */
    public const DEFAULT_WRAP_INTERSECTION_TYPE = true;

    /**
     * @var bool
     */
    public const DEFAULT_WRAP_UNION_TYPE = true;

    /**
     * @var bool
     */
    public const DEFAULT_WRAP_CALLABLE_RETURN_TYPE = true;

    /**
     * @var int<0, max>
     */
    public const DEFAULT_MULTILINE_SHAPE = 1;

    /**
     * @param non-empty-string $newLine
     * @param non-empty-string $indention
     */
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
        public bool $wrapUnionType = self::DEFAULT_WRAP_UNION_TYPE,
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
        public bool $wrapIntersectionType = self::DEFAULT_WRAP_INTERSECTION_TYPE,
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
        public bool $wrapCallableReturnType = self::DEFAULT_WRAP_CALLABLE_RETURN_TYPE,
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
        public int $multilineShape = self::DEFAULT_MULTILINE_SHAPE,
    ) {
        parent::__construct($newLine, $indention);
    }

    /**
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function make(Statement $stmt): string
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
            $stmt instanceof TernaryConditionNode => $this->printTernaryType($stmt),
            $stmt instanceof TypesListNode => $this->printTypeListNode($stmt),
            $stmt instanceof TypeOffsetAccessNode => $this->printTypeOffsetAccessNode($stmt),
            default => throw NonPrintableNodeException::fromInvalidNode($stmt),
        };
    }

    /**
     * @param LiteralNode<mixed> $node
     *
     * @return non-empty-string
     */
    protected function printLiteralNode(LiteralNode $node): string
    {
        /** @var non-empty-string */
        return $node->getRawValue();
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
            $current = '';

            if ($field->attributes !== null) {
                $current .= $this->printAttributeGroups($field->attributes, $multiline);
            }

            $fields[] = $current . $prefix . $this->printShapeFieldNode($field);
        }

        if (!$shape->sealed || $node->arguments !== null) {
            $prefix .= '...';

            if ($node->arguments !== null) {
                $prefix .= $this->printTemplateArgumentsNode($node->arguments);
            }

            $fields[] = $prefix;
        }

        /** @var list<non-empty-string> */
        return $fields;
    }

    protected function printAttributeGroups(AttributeGroupsListNode $groups, bool $multiline): string
    {
        $prefix = $this->prefix();
        $result = '';

        foreach ($groups as $group) {
            $result .= $prefix . $this->printAttributeGroup($group);
            $result .= $multiline ? $this->newLine : ' ';
        }

        return $result;
    }

    protected function printAttributeGroup(AttributeGroupNode $group): string
    {
        $result = '#[';

        $last = $group->last();
        foreach ($group as $attribute) {
            $result .= $attribute->name->toString();

            if ($attribute !== $last) {
                $result .= ', ';
            }
        }

        return $result . ']';
    }

    /**
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printShapeFieldNode(FieldNode $field): string
    {
        $name = $this->printShapeFieldName($field);

        if ($name !== '') {
            if ($field->optional) {
                $name .= '?';
            }

            return \vsprintf('%s: %s', [
                $name,
                $this->make($field->getType()),
            ]);
        }

        /** @var non-empty-string */
        return $this->make($field->getType());
    }

    protected function printShapeFieldName(FieldNode $field): string
    {
        return match (true) {
            $field instanceof StringNamedFieldNode => $this->printStringShapeFieldName($field),
            $field instanceof NumericFieldNode => $this->printNumericShapeFieldName($field),
            $field instanceof NamedFieldNode => $this->printNamedShapeFieldName($field),
            $field instanceof ConstMaskFieldNode => $this->printConstMaskShapeFieldName($field),
            $field instanceof ClassConstMaskFieldNode => $this->printClassConstMaskShapeFieldName($field),
            default => $this->printUnknownShapeFieldName($field),
        };
    }

    protected function printStringShapeFieldName(StringNamedFieldNode $field): string
    {
        return $field->key->getRawValue();
    }

    protected function printNumericShapeFieldName(NumericFieldNode $field): string
    {
        return $field->key->getRawValue();
    }

    protected function printNamedShapeFieldName(NamedFieldNode $field): string
    {
        return $field->key->toString();
    }

    protected function printConstMaskShapeFieldName(ConstMaskFieldNode $field): string
    {
        return $field->key->name->toString() . '*';
    }

    protected function printClassConstMaskShapeFieldName(ClassConstMaskFieldNode $field): string
    {
        $class = $field->key->class;
        $constant = $field->key->constant;

        if ($field->key instanceof ClassConstNode) {
            return $class . '::' . $constant;
        }

        if ($constant === null) {
            return $class . '::*';
        }

        return $class . '::' . $constant . '*';
    }

    protected function printUnknownShapeFieldName(FieldNode $field): string
    {
        return '';
    }

    /**
     * @param ArgumentsListNode<ArgumentNode>|TemplateArgumentsListNode $arguments
     *
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printTemplateArgumentsNode(ArgumentsListNode $arguments): string
    {
        $result = [];

        foreach ($arguments as $argument) {
            $current = '';

            if ($argument->attributes !== null) {
                $current .= $this->printAttributeGroups($argument->attributes, false);
            }

            $result[] = $current . $this->printTemplateArgumentNode($argument);
        }

        return \sprintf('<%s>', \implode(', ', $result));
    }

    /**
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printTemplateArgumentNode(ArgumentNode $argument): string
    {
        $result = $this->make($argument->value);

        if ($argument->hint !== null) {
            return $argument->hint->toString() . ' ' . $result;
        }

        return $result;
    }

    /**
     * @return non-empty-string
     */
    protected function printClassConstNode(ClassConstNode $node): string
    {
        return \vsprintf('%s::%s', [
            $node->class->toString(),
            (string) $node->constant?->toString(),
        ]);
    }

    /**
     * @return non-empty-string
     */
    protected function printClassConstMaskNode(ClassConstMaskNode $node): string
    {
        return \vsprintf('%s::%s', [
            $node->class->toString(),
            (string) $node->constant?->toString() . '*',
        ]);
    }

    /**
     * @return non-empty-string
     */
    protected function printConstMaskNode(ConstMaskNode $node): string
    {
        return $node->name->toString() . '*';
    }

    /**
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printCallableTypeNode(CallableTypeNode $node): string
    {
        $result = $node->name->toString();

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
    protected function printCallableArgumentNode(ParameterNode $node): string
    {
        $result = 'mixed';

        if ($node->type !== null) {
            $result = $this->make($node->type);
        }

        if ($node->attributes !== null) {
            $result = $this->printAttributeGroups($node->attributes, false)
                . $result;
        }

        if ($node->name !== null) {
            $result .= ' ';
        }

        if ($node->output) {
            $result .= '&';
        }

        if ($node->variadic) {
            $result .= '...';
        }

        if ($node->name !== null) {
            // @phpstan-ignore-next-line : VariableLiteralNode is a subtype of LiteralNode
            $result .= $this->printLiteralNode($node->name);
        }

        if ($node->optional) {
            $result .= '=';
        }

        return $result;
    }

    protected function shouldWrapReturnType(TypeStatement $type): bool
    {
        if ($type instanceof LogicalTypeNode) {
            return true;
        }

        $visitor = Traverser::through(
            visitor: new Traverser\ClassNameMatcherVisitor(
                class: LogicalTypeNode::class,
                break: static function (Node $node): bool {
                    // Break on non-empty template parameters.
                    $isInTemplate = $node instanceof NamedTypeNode
                        && $node->arguments !== null
                        && $node->arguments->items !== [];

                    // Break on non-empty shape fields.
                    $isInShape = $node instanceof NamedTypeNode
                        && $node->fields !== null
                        && $node->fields->items !== [];

                    return $isInTemplate || $isInShape;
                },
            ),
            nodes: [$type],
        );

        return $visitor->isFound();
    }

    /**
     * @param UnionTypeNode<TypeStatement> $node
     *
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
     * @param IntersectionTypeNode<TypeStatement> $node
     *
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
     * @param NullableTypeNode<TypeStatement> $node
     *
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
    protected function printTernaryType(TernaryConditionNode $node): string
    {
        return \vsprintf('(%s %s %s ? %s : %s)', [
            $this->make($node->condition->subject),
            $this->printCondition($node->condition),
            $this->make($node->condition->target),
            $this->make($node->then),
            $this->make($node->else),
        ]);
    }

    /**
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printCondition(Condition $node): string
    {
        return match (true) {
            $node instanceof EqualConditionNode => 'is',
            $node instanceof NotEqualConditionNode => 'is not',
            $node instanceof GreaterOrEqualThanConditionNode => '>=',
            $node instanceof LessOrEqualThanConditionNode => '<=',
            $node instanceof GreaterThanConditionNode => '>',
            $node instanceof LessThanConditionNode => '<',
            default => throw NonPrintableNodeException::fromInvalidNode($node),
        };
    }

    /**
     * @param TypesListNode<TypeStatement> $node
     *
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printTypeListNode(TypesListNode $node): string
    {
        $result = $this->make($node->type);

        return $result . '[]';
    }

    /**
     * @param TypeOffsetAccessNode<TypeStatement> $node
     *
     * @return non-empty-string
     * @throws NonPrintableNodeException
     */
    protected function printTypeOffsetAccessNode(TypeOffsetAccessNode $node): string
    {
        $result = $this->make($node->type);

        return $result . '[' . $this->make($node->access) . ']';
    }
}
