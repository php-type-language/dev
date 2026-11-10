<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor\Exception;

/**
 * @template TSubject of object = object
 *
 * @template-implements PropertyAccessorExceptionInterface<TSubject>
 */
abstract class PropertyAccessException extends \RuntimeException implements
    PropertyAccessorExceptionInterface
{
    public function __construct(
        /**
         * Object whose property was accessed
         *
         * @var TSubject
         */
        public readonly object $object,
        /**
         * Name of the accessed property
         */
        public readonly string $property,
        string $message,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * Returns the property reference for a message, such as `Foo::$bar`.
     */
    protected static function formatProperty(object $object, string $property): string
    {
        return $object::class . '::$' . $property;
    }
}
