<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator;

use TypeLang\Mapper\Instantiator\Exception\ConstructionFailedException;

/**
 * Creates objects by calling the public constructor.
 *
 * @template T of object
 *
 * @template-implements InstantiatorInterface<T>
 */
final class ConstructorInstantiator implements InstantiatorInterface
{
    public function __construct(
        /**
         * @var class-string<T>
         */
        private readonly string $class,
    ) {}

    /**
     * @throws ConstructionFailedException in case of the constructor has
     *         thrown
     */
    public function instantiate(array $arguments = []): object
    {
        try {
            return new ($this->class)(...$arguments);
        } catch (\Throwable $e) {
            throw ConstructionFailedException::becauseConstructorHasThrown($this->class, $e);
        }
    }
}
