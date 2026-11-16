<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator;

use TypeLang\Mapper\Instantiator\Exception\ClassNotInstantiableException;

/**
 * Creates the objects of any class without calling the constructor.
 */
final class InstantiatorFactory implements InstantiatorFactoryInterface
{
    /**
     * @throws ClassNotInstantiableException in case of the class is
     *         abstract, an interface, a trait, an enum or an internal
     *         class requiring the constructor
     */
    public function createInstantiator(\ReflectionClass $class): InstantiatorInterface
    {
        if ($class->isInterface()) {
            throw ClassNotInstantiableException::becauseClassIsInterface($class->name);
        }

        if ($class->isTrait()) {
            throw ClassNotInstantiableException::becauseClassIsTrait($class->name);
        }

        if ($class->isEnum()) {
            throw ClassNotInstantiableException::becauseClassIsEnum($class->name);
        }

        if ($class->isAbstract()) {
            throw ClassNotInstantiableException::becauseClassIsAbstract($class->name);
        }

        $instantiator = new WithoutConstructorInstantiator($class);

        // Probe of an internal final class, such a class may require the
        // constructor.
        if ($class->isInternal() && $class->isFinal()) {
            try {
                $instantiator->instantiate();
            } catch (\ReflectionException $e) {
                throw ClassNotInstantiableException::becauseClassRequiresConstructor($class->name, $e);
            }
        }

        return $instantiator;
    }
}
