<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Printer\NodePrinter\Registry\NodePrinterRegistryInterface;
use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Type\TypeNode;

/**
 * Prints a type with the printers of its nodes.
 */
final class TypePrinter implements TypePrinterInterface
{
    public function __construct(
        /**
         * The printers of the nodes.
         */
        public readonly NodePrinterRegistryInterface $registry,
        /**
         * The formatting settings.
         */
        public readonly PrinterOptions $options = new PrinterOptions(),
    ) {}

    /**
     * @throws PrinterExceptionInterface in case of a node of the type cannot be printed
     */
    public function print(TypeNode $stmt): string
    {
        return (new PrinterContext($this->options, $this->registry))
            ->print($stmt);
    }
}
