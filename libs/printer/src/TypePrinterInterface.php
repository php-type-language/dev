<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Type\TypeNode;

interface TypePrinterInterface
{
    /**
     * @return non-empty-string
     */
    public function print(TypeNode $stmt): string;
}
