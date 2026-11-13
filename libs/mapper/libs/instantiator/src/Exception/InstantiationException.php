<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator\Exception;

/**
 * Failure of the instantiator, carries the name of the class.
 */
abstract class InstantiationException extends \RuntimeException implements
    InstantiatorExceptionInterface
{
    public function __construct(
        /**
         * Name of the class whose object is created, the class may not exist
         */
        public readonly string $class,
        string $message,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * Returns the method reference for a message, such as `Foo::bar()`.
     */
    protected static function formatMethod(string $class, string $method): string
    {
        return \addcslashes($class, '"') . '::' . \addcslashes($method, '"') . '()';
    }
}
