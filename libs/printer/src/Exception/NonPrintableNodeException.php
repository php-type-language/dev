<?php

declare(strict_types=1);

namespace TypeLang\Printer\Exception;

use TypeLang\Parser\Node\Node;

class NonPrintableNodeException extends \InvalidArgumentException implements PrinterExceptionInterface
{
    final public const ERROR_CODE_INVALID_NODE = 0x01;

    protected const CODE_LAST = self::ERROR_CODE_INVALID_NODE;

    final public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromInvalidNode(Node $node): self
    {
        $message = \sprintf('Could not print unknown node "%s"', $node::class);

        return new static($message, self::ERROR_CODE_INVALID_NODE);
    }
}
