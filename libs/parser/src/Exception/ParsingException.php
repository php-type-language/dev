<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Source\ReadableInterface;

abstract class ParsingException extends ParserException implements
    ParserRuntimeExceptionInterface
{
    public function __construct(
        public readonly TokenInterface $token,
        ReadableInterface $source,
        string $message,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($source, $message, $code, $previous);
    }
}
