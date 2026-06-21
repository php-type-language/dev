<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Node\Type\TypeNode;

interface PrinterInterface
{
    public function print(TypeNode $stmt): string;
}
