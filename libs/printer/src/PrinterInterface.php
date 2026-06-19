<?php

declare(strict_types=1);

namespace TypeLang\Printer;

use TypeLang\Parser\Node\Statement;

interface PrinterInterface
{
    /**
     * @return non-empty-string
     */
    public function print(Statement $stmt): string;
}
