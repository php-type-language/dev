<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Parser\Node\Literal\BoolLiteralNode;
use TypeLang\Parser\Node\Literal\FloatLiteralNode;
use TypeLang\Parser\Node\Literal\IntLiteralNode;
use TypeLang\Parser\Node\Literal\LiteralNode;
use TypeLang\Parser\Node\Literal\NullLiteralNode;
use TypeLang\Parser\Node\Literal\StringLiteralNode;
use TypeLang\Parser\Node\Literal\VariableLiteralNode;
use TypeLang\Parser\Node\Stmt\CallableTypeNode;
use TypeLang\Parser\Node\Stmt\ClassConstMaskNode;
use TypeLang\Parser\Node\Stmt\ClassConstNode;
use TypeLang\Parser\Node\Stmt\Condition\Condition;
use TypeLang\Parser\Node\Stmt\Condition\EqualConditionNode;
use TypeLang\Parser\Node\Stmt\Condition\NotEqualConditionNode;
use TypeLang\Parser\Node\Stmt\ConstMaskNode;
use TypeLang\Parser\Node\Stmt\IntersectionTypeNode;
use TypeLang\Parser\Node\Stmt\NamedTypeNode;
use TypeLang\Parser\Node\Stmt\TernaryConditionNode;
use TypeLang\Parser\Node\Stmt\TypeOffsetAccessNode;
use TypeLang\Parser\Node\Stmt\TypesListNode;
use TypeLang\Parser\Node\Stmt\UnionTypeNode;
use TypeLang\Printer\Exception\NonPrintableNodeException;

class NativeTypePrinter extends PrettyPrinter
{
    /**
     * @var array<non-empty-string, non-empty-string>
     */
    private array $aliases = [];

    /**
     * @param iterable<non-empty-string, non-empty-string> $aliases
     * @param non-empty-string $newLine
     * @param non-empty-string $indention
     */
    public function __construct(
        iterable $aliases = [],
        string $newLine = self::DEFAULT_NEW_LINE_DELIMITER,
        string $indention = self::DEFAULT_INDENTION,
    ) {
        parent::__construct($newLine, $indention);

        // preload phan type aliases
        // @phpstan-ignore-next-line : PHPStan false-positive, not a bug
        $this->aliases += require __DIR__ . '/../resources/aliases/phan.php';
        // preload psalm type aliases
        // @phpstan-ignore-next-line : PHPStan false-positive, not a bug
        $this->aliases += require __DIR__ . '/../resources/aliases/psalm.php';
        // preload phpstan type aliases
        // @phpstan-ignore-next-line : PHPStan false-positive, not a bug
        $this->aliases += require __DIR__ . '/../resources/aliases/phpstan.php';

        foreach ($aliases as $alias => $type) {
            $this->addTypeAlias($alias, $type);
        }
    }

    /**
     * @api
     *
     * @param non-empty-string $alias
     * @param non-empty-string $type
     */
    public function addTypeAlias(string $alias, string $type): void
    {
        $this->aliases[\strtolower($alias)] = $type;
    }

    /**
     * @api
     *
     * @param non-empty-string $alias
     * @param non-empty-list<non-empty-string> $types
     */
    public function addUnionTypeAlias(string $alias, array $types): void
    {
        \sort($types);

        $this->aliases[\strtolower($alias)] = \implode('|', $types);
    }

    /**
     * @api
     *
     * @param non-empty-string $alias
     * @param non-empty-list<non-empty-string> $types
     */
    public function addIntersectionTypeAlias(string $alias, array $types): void
    {
        \sort($types);

        $this->aliases[\strtolower($alias)] = \implode('&', $types);
    }

    /**
     * @return non-empty-string
     */
    #[\Override]
    protected function printTypeListNode(TypesListNode $node): string
    {
        return 'iterable';
    }

    #[\Override]
    protected function printTernaryType(TernaryConditionNode $node): string
    {
        return $this->make(new UnionTypeNode($node->then, $node->else));
    }

    #[\Override]
    protected function printCondition(Condition $node): string
    {
        return match (true) {
            $node instanceof EqualConditionNode => '===',
            $node instanceof NotEqualConditionNode => '!==',
            default => throw NonPrintableNodeException::fromInvalidNode($node),
        };
    }

    #[\Override]
    protected function printClassConstMaskNode(ClassConstMaskNode $node): string
    {
        return 'mixed';
    }

    #[\Override]
    protected function printConstMaskNode(ConstMaskNode $node): string
    {
        return 'mixed';
    }

    #[\Override]
    protected function printClassConstNode(ClassConstNode $node): string
    {
        return 'mixed';
    }

    #[\Override]
    protected function printUnionTypeNode(UnionTypeNode $node): string
    {
        $shouldWrap = $this->nesting++ > 0;

        $result = $this->unwrapAndPrint($node);

        $result = $this->formatUnionWithMixed($result);
        $result = $this->formatBoolWithTrueAndFalse($result);

        /** @var non-empty-string */
        return \vsprintf($shouldWrap ? '(%s)' : '%s', [
            \implode('|', [...\array_unique($result)]),
        ]);
    }

    /**
     * Replace "true" + "false" pair into "bool"
     *
     * @param list<non-empty-string> $result
     *
     * @return list<non-empty-string>
     */
    private function formatBoolWithTrueAndFalse(array $result): array
    {
        $containsBool = (\in_array('true', $result, true) || \in_array('\true', $result, true))
            && (\in_array('false', $result, true) || \in_array('\false', $result, true));

        if (!$containsBool) {
            return $result;
        }

        $filtered = \array_filter($result, static fn(string $type): bool
            => !\in_array($type, ['true', 'false', '\true', '\false'], true));
        $filtered[] = 'bool';

        /** @var list<non-empty-string> */
        return $filtered;
    }

    /**
     * Replace everything that contain "mixed" type
     * if one of the types is "mixed".
     *
     * @param list<non-empty-string> $result
     *
     * @return list<non-empty-string>
     */
    private function formatUnionWithMixed(array $result): array
    {
        if (\in_array('mixed', $result, true)) {
            return ['mixed'];
        }

        return $result;
    }

    #[\Override]
    protected function printIntersectionTypeNode(IntersectionTypeNode $node): string
    {
        /** @var non-empty-string */
        return \vsprintf($this->nesting++ > 0 ? '(%s)' : '%s', [
            \implode('&', [...$this->unwrapAndPrint($node)]),
        ]);
    }

    #[\Override]
    protected function printCallableTypeNode(CallableTypeNode $node): string
    {
        return $this->getTypeName($node->name->toString());
    }

    /**
     * @param non-empty-string $name
     *
     * @return non-empty-string
     */
    protected function getTypeName(string $name): string
    {
        return $this->aliases[\strtolower($name)] ?? $name;
    }

    #[\Override]
    protected function printNamedTypeNode(NamedTypeNode $node): string
    {
        return $this->getTypeName($node->name->toString());
    }

    #[\Override]
    protected function printLiteralNode(LiteralNode $node): string
    {
        /** @var non-empty-string */
        return match (true) {
            $node instanceof BoolLiteralNode => 'bool',
            $node instanceof FloatLiteralNode => 'float',
            $node instanceof IntLiteralNode => 'int',
            $node instanceof NullLiteralNode => 'null',
            $node instanceof StringLiteralNode => 'string',
            $node instanceof VariableLiteralNode => $node->getValue() === 'this' ? 'self' : 'mixed',
            default => \get_debug_type($node->getValue()),
        };
    }

    #[\Override]
    protected function printTypeOffsetAccessNode(TypeOffsetAccessNode $node): string
    {
        // Getting an offset requires information about the data from which the
        // offset is needed. Therefore, we return a mixed type.
        return 'mixed';
    }
}
