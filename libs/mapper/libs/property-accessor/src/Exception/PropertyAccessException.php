<?php

declare(strict_types=1);

namespace TypeLang\PropertyAccessor\Exception;

abstract class PropertyAccessException extends \LogicException implements
    PropertyAccessorExceptionInterface
{
    public function __construct(
        public readonly object $object,
        public readonly string $property,
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
