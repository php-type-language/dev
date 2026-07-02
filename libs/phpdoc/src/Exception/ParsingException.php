<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Exception;

abstract class ParsingException extends \RuntimeException implements ParsingExceptionInterface
{
    final public function __construct(
        public readonly string $source,
        /**
         * @var int<0, max>
         */
        public readonly int $offset = 0,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
