<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator;

use TypeLang\Mapper\Instantiator\Exception\InstantiatorExceptionInterface;

/**
 * Strategy of creating the objects of a class, chosen once by the
 * declaration of the class, such as taking the objects from a DI container.
 * A class whose objects cannot be created is rejected on creation of its
 * instantiator.
 */
interface InstantiatorFactoryInterface
{
    /**
     * @template TArgObject of object
     * @param \ReflectionClass<TArgObject> $class
     * @return InstantiatorInterface<TArgObject>
     * @throws InstantiatorExceptionInterface in case of the objects of the
     *         class cannot be created
     */
    public function createInstantiator(\ReflectionClass $class): InstantiatorInterface;
}
