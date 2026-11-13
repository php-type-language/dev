<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator;

use TypeLang\Mapper\Instantiator\Exception\ConstructionFailedException;

/**
 * Creates objects of the class the instantiator is bound to.
 *
 * @template-covariant T of object
 */
interface InstantiatorInterface
{
    /**
     * Creates a new object. Integer keys of the arguments are passed
     * positionally and must go first, string keys are passed by name.
     *
     * @param array<array-key, mixed> $arguments
     * @return T
     * @throws ConstructionFailedException in case of the constructor or the
     *         static method has thrown or returned not an instance of T
     */
    public function instantiate(array $arguments = []): object;
}
