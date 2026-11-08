<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter;

use TypeLang\Printer\NativeTypePrinter;
use TypeLang\Printer\NodePrinter\Registry\NodePrinterRegistry;
use TypeLang\Printer\PrettyTypePrinter;
use TypeLang\Printer\PrinterContext;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Printer\Tests\TestCase;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\NamespaceName;
use TypeLang\Type\NamespaceName\NamespaceKind;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

/**
 * The base of the tests of the node printers, which build the nodes by hand
 * and so do not depend on the parser.
 */
abstract class NodePrinterTestCase extends TestCase
{
    /**
     * Builds a named type out of a name written as a string: `int`,
     * `Some\Any`, `\Some\Any`, `\true`.
     *
     * @param non-empty-string $name
     */
    protected static function named(string $name): NamedTypeNode
    {
        $separator = \strrpos($name, '\\');

        if ($separator === false) {
            return new NamedTypeNode(new Name($name));
        }

        /** @var non-empty-string $word */
        $word = \substr($name, $separator + 1);

        $kind = NamespaceKind::Qualified;

        if ($name[0] === '\\') {
            $kind = NamespaceKind::FullyQualified;
        }

        $namespace = new NamespaceName(
            value: \ltrim(\substr($name, 0, $separator), '\\'),
            kind: $kind,
        );

        return new NamedTypeNode(new Name($word, $namespace));
    }

    protected static function union(TypeNode ...$types): UnionTypeNode
    {
        return new UnionTypeNode($types);
    }

    /**
     * Prints a node with the printers of the {@see PrettyTypePrinter}.
     */
    protected static function pretty(Node $node, PrinterOptions $options = new PrinterOptions()): string
    {
        return (new PrinterContext($options, PrettyTypePrinter::createRegistry()))->print($node);
    }

    /**
     * Prints a node with the printers of the {@see NativeTypePrinter}.
     *
     * @param iterable<non-empty-string, non-empty-string> $aliases
     */
    protected static function native(TypeNode $node, iterable $aliases = []): string
    {
        return (new NativeTypePrinter($aliases))->print($node);
    }

    /**
     * Gets a registry with no printers at all.
     */
    protected static function emptyRegistry(): NodePrinterRegistry
    {
        return new NodePrinterRegistry();
    }
}
