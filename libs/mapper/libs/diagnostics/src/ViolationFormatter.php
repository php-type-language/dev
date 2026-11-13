<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Diagnostics;

use TypeLang\Mapper\Diagnostics\Printer\Value\ShortValuePrinter;
use TypeLang\Mapper\Diagnostics\Printer\Value\ValuePrinterInterface;

/**
 * Renders the text of a violation without its path.
 */
final class ViolationFormatter
{
    public function __construct(
        private readonly ValuePrinterInterface $valuePrinter = new ShortValuePrinter(),
    ) {}

    /**
     * Unknown placeholders of the template stay as they are.
     */
    public function format(Violation $violation): string
    {
        $replacements = [];

        foreach ($violation->parameters as $name => $parameter) {
            $replacements['{{' . $name . '}}'] = (string) $parameter;
        }

        $replacements['{{value}}'] = $this->valuePrinter->print($violation->value);

        return \strtr($violation->code->getTemplate(), $replacements);
    }
}
