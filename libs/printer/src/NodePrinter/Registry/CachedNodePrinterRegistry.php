<?php

declare(strict_types=1);

namespace TypeLang\Printer\NodePrinter\Registry;

use TypeLang\Printer\NodePrinterInterface;
use TypeLang\Type\Node;

/**
 * Remembers the printer found for a node class.
 */
final class CachedNodePrinterRegistry implements NodePrinterRegistryInterface
{
    /**
     * @var array<class-string<Node>, NodePrinterInterface<covariant Node>>
     */
    private array $printers = [];

    public function __construct(
        /**
         * The registry that finds a printer not found yet.
         */
        private readonly NodePrinterRegistryInterface $delegate,
    ) {}

    public function getPrinter(Node $node): NodePrinterInterface
    {
        return $this->printers[$node::class] ??= $this->delegate->getPrinter($node);
    }
}
