<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Registry;

use TypeLang\Printer\Exception\NonPrintableNodeException;
use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Type\Node;

interface NodePrinterRegistryInterface
{
    /**
     * @return NodePrinterInterface<covariant Node>
     * @throws NonPrintableNodeException in case of no printer is registered for the node
     */
    public function getPrinter(Node $node): NodePrinterInterface;
}
