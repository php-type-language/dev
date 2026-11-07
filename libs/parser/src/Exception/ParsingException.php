<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Lexer\TokenInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Exception\ErrorPrinter;

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
        parent::__construct(
            source: $source,
            message: $message,
            code: $code,
            previous: $previous,
        );
    }

    public function __toString(): string
    {
        try {
            return (string) (new ErrorPrinter())
                ->print($this)
                ->withSource($this->source)
                ->withInterval($this->token->offset, $this->token->size);
        } catch (\Throwable) {
            return parent::__toString();
        }
    }
}
