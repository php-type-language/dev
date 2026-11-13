<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator;

use TypeLang\Mapper\Instantiator\Exception\ConstructionFailedException;

/**
 * Creates objects by calling a public static method of the class, such as
 * `Money::fromString()`.
 *
 * @template T of object
 *
 * @template-implements InstantiatorInterface<T>
 */
final class StaticMethodInstantiator implements InstantiatorInterface
{
    public function __construct(
        /**
         * @var class-string<T>
         */
        private readonly string $class,
        /**
         * @var non-empty-string
         */
        private readonly string $method,
        private readonly \Closure $callable,
    ) {}

    /**
     * @throws ConstructionFailedException in case of the method has thrown
     *         or returned not an instance of T
     */
    public function instantiate(array $arguments = []): object
    {
        try {
            $result = ($this->callable)(...$arguments);
        } catch (\Throwable $e) {
            throw ConstructionFailedException::becauseMethodHasThrown($this->class, $this->method, $e);
        }

        if ($result instanceof $this->class) {
            return $result;
        }

        throw ConstructionFailedException::becauseMethodReturnedInvalidValue($this->class, $this->method, $result);
    }
}
