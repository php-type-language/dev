<?php

declare(strict_types=1);

namespace TypeLang\Printer\Exception;

use TypeLang\Type\Node;

final class NonPrintableNodeException extends PrinterException
{
    public static function becauseInvalidNodeGiven(Node $node, ?\Throwable $previous = null): self
    {
        $message = \sprintf('Could not print unknown node "%s"', $node::class);

        return new self($message, 0, $previous);
    }
}
