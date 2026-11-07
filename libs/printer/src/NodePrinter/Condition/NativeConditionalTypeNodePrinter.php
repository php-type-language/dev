<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Condition;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Printer\PrinterContext;
use TypeLang\Type\Condition\ConditionalTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\Node;
use TypeLang\Type\TypeNode;

/**
 * Prints a conditional type as the union of the results of all its branches.
 *
 * @template-implements NodePrinterInterface<ConditionalTypeNode>
 */
final class NativeConditionalTypeNodePrinter implements NodePrinterInterface
{
    /**
     * @throws PrinterExceptionInterface in case of a child node cannot be printed
     */
    public function printNode(Node $node, PrinterContext $context): string
    {
        return $context->print(new UnionTypeNode($this->getResults($node)));
    }

    /**
     * Gets the results of a conditional type, looking into the nested ones.
     *
     * @return non-empty-list<TypeNode>
     */
    private function getResults(ConditionalTypeNode $node): array
    {
        $results = [];

        foreach ([$node->then, $node->else] as $result) {
            if ($result instanceof ConditionalTypeNode) {
                \array_push($results, ...$this->getResults($result));
            } else {
                $results[] = $result;
            }
        }

        return $results;
    }
}
