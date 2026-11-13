<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Diagnostics\Tests\Stub;

use TypeLang\Mapper\Diagnostics\Exception\ViolationException;
use TypeLang\Mapper\Diagnostics\Printer\Path\JsonPathPrinter;
use TypeLang\Mapper\Diagnostics\Printer\Path\PathPrinterInterface;
use TypeLang\Mapper\Diagnostics\Violation;
use TypeLang\Mapper\Diagnostics\ViolationFormatter;

final class InvalidInputStubException extends ViolationException
{
    /**
     * @param non-empty-list<Violation> $violations
     */
    public static function becauseInputIsInvalid(
        array $violations,
        ViolationFormatter $formatter = new ViolationFormatter(),
        PathPrinterInterface $pathPrinter = new JsonPathPrinter(),
    ): self {
        return new self($violations, self::formatSummary($violations, $formatter, $pathPrinter));
    }
}
