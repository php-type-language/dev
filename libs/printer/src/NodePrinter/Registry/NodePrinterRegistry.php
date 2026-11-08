<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Registry;

use TypeLang\Printer\Exception\NonPrintableNodeException;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Type\Node;

/**
 * Finds a printer of a node by its class, or by the closest parent class
 * a printer is registered for.
 */
final class NodePrinterRegistry implements NodePrinterRegistryInterface
{
    public function __construct(
        /**
         * The printers by the class of the node they print.
         *
         * @var array<class-string<Node>, NodePrinterInterface<covariant Node>>
         */
        private readonly array $printers = [],
    ) {}

    /**
     * Gets a registry with the printer of the node class added, or replaced.
     *
     * @template TNode of Node
     * @param class-string<TNode> $node
     * @param NodePrinterInterface<covariant TNode> $printer
     */
    public function with(string $node, NodePrinterInterface $printer): self
    {
        return new self([...$this->printers, $node => $printer]);
    }

    public function getPrinter(Node $node): NodePrinterInterface
    {
        $class = $node::class;

        do {
            if (isset($this->printers[$class])) {
                return $this->printers[$class];
            }
        } while (($class = \get_parent_class($class)) !== false);

        throw NonPrintableNodeException::becauseInvalidNodeGiven($node);
    }
}
