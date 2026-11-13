<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator;

/**
 * Creates objects without calling the constructor.
 *
 * @template T of object
 *
 * @template-implements InstantiatorInterface<T>
 */
final class WithoutConstructorInstantiator implements InstantiatorInterface
{
    public function __construct(
        /**
         * @var \ReflectionClass<T>
         */
        private readonly \ReflectionClass $reflection,
    ) {}

    public function instantiate(array $arguments = []): object
    {
        \assert($arguments === [], 'An object created without the constructor accepts no arguments');

        return $this->reflection->newInstanceWithoutConstructor();
    }
}
