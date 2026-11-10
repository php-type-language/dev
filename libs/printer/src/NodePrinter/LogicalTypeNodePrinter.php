<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Type\LogicalTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

/**
 * Prints the types a union or an intersection is made of, joined by the
 * delimiter. The result is put in brackets in case of the node is nested into
 * another logical type.
 *
 * @template TLogical of LogicalTypeNode
 *
 * @template-implements NodePrinterInterface<TLogical>
 */
abstract class LogicalTypeNodePrinter implements NodePrinterInterface
{
    /**
     * @return non-empty-string
     */
    abstract protected function getDelimiter(PrinterOptions $options): string;

    /**
     * Gets the printed types of a logical type, which a subclass may rewrite.
     *
     * @param list<string> $types
     * @return list<string>
     */
    protected function normalize(array $types): array
    {
        return $types;
    }

    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        $isNested = $context->isInsideLogical();

        $types = $context->groupLogical(fn(): array => $this->printTypes($node, $context));

        $types = $this->normalize($types);
        $result = \implode($this->getDelimiter($context->options), $types);

        return $isNested && \count($types) > 1 ? '(' . $result . ')' : $result;
    }

    /**
     * @param LogicalTypeNode<TypeNode> $node
     * @return list<string>
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    private function printTypes(LogicalTypeNode $node, PrinterContext $context): array
    {
        $result = [];

        foreach ($this->unwrap($node) as $type) {
            $result[] = $context->print($type);
        }

        return \array_values(\array_unique($result));
    }

    /**
     * @template T of TypeNode
     * @param LogicalTypeNode<T> $logical
     * @return iterable<array-key, T>
     */
    private function unwrap(LogicalTypeNode $logical): iterable
    {
        foreach ($logical->children as $type) {
            if ($type instanceof $logical) {
                yield from $this->unwrap($type);
            } else {
                yield $type;
            }
        }
    }
}
