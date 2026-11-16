<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator;

/**
 * Creates new objects of its class, the mapped properties are written
 * after an object is created. The way of creating is chosen once by
 * {@see InstantiatorFactoryInterface::createInstantiator()}.
 *
 * ```
 * $class = new \ReflectionClass(User::class);
 * $instantiator = $factory->createInstantiator($class);
 *
 * $user = $instantiator->instantiate();
 * ```
 *
 * @template-covariant T of object
 */
interface InstantiatorInterface
{
    /**
     * @return T
     */
    public function instantiate(): object;
}
