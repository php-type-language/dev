<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Printer\Exception\PrinterExceptionInterface;
use TypeLang\Type\TypeNode;

interface TypePrinterInterface
{
    /**
     * @return non-empty-string
     * @throws PrinterExceptionInterface in case of a node of the type cannot
     *         be printed
     */
    public function print(TypeNode $stmt): string;
}
