<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Instantiator;

use TypeLang\Mapper\Instantiator\Exception\ClassNotFoundException;
use TypeLang\Mapper\Instantiator\Exception\ClassNotInstantiableException;
use TypeLang\Mapper\Instantiator\Exception\MethodNotCallableException;

/**
 * Creates instantiators bound to a class.
 */
final class InstantiatorFactory
{
    /**
     * Returns an instantiator that skips the constructor.
     *
     * @template T of object
     * @param class-string<T> $class
     * @return InstantiatorInterface<T>
     * @throws ClassNotFoundException in case of the class does not exist
     * @throws ClassNotInstantiableException in case of the class is
     *         abstract, an interface, a trait, an enum or an internal
     *         final class
     */
    public function createWithoutConstructor(string $class): InstantiatorInterface
    {
        $reflection = self::getInstantiableClass($class);

        if ($reflection->isInternal() && $reflection->isFinal()) {
            throw ClassNotInstantiableException::becauseClassIsInternalFinal($class);
        }

        return new WithoutConstructorInstantiator($reflection);
    }

    /**
     * Returns an instantiator that calls the constructor.
     *
     * @template T of object
     * @param class-string<T> $class
     * @return InstantiatorInterface<T>
     * @throws ClassNotFoundException in case of the class does not exist
     * @throws ClassNotInstantiableException in case of the class is
     *         abstract, an interface, a trait or an enum
     * @throws MethodNotCallableException in case of the constructor is not
     *         public
     */
    public function createFromConstructor(string $class): InstantiatorInterface
    {
        $constructor = self::getInstantiableClass($class)->getConstructor();

        if ($constructor !== null && !$constructor->isPublic()) {
            throw MethodNotCallableException::becauseConstructorIsNotPublic($class);
        }

        return new ConstructorInstantiator($class);
    }

    /**
     * Returns an instantiator that calls a public static method of the
     * class, such as `Money::fromString()`.
     *
     * @template T of object
     * @param class-string<T> $class
     * @param non-empty-string $method
     * @return InstantiatorInterface<T>
     * @throws ClassNotFoundException in case of the class does not exist
     * @throws MethodNotCallableException in case of the method does not
     *         exist, is not public, not static or abstract
     */
    public function createFromStaticMethod(string $class, string $method): InstantiatorInterface
    {
        $reflection = self::getClass($class);

        if (!$reflection->hasMethod($method)) {
            throw MethodNotCallableException::becauseMethodDoesNotExist($class, $method);
        }

        $function = $reflection->getMethod($method);

        if (!$function->isPublic()) {
            throw MethodNotCallableException::becauseMethodIsNotPublic($class, $method);
        }

        if (!$function->isStatic()) {
            throw MethodNotCallableException::becauseMethodIsNotStatic($class, $method);
        }

        if ($function->isAbstract()) {
            throw MethodNotCallableException::becauseMethodIsAbstract($class, $method);
        }

        // @phpstan-ignore-next-line the method is checked to be public static
        $callable = \Closure::fromCallable([$class, $method]);

        return new StaticMethodInstantiator($class, $method, $callable);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return \ReflectionClass<T>
     * @throws ClassNotFoundException in case of the class does not exist
     */
    private static function getClass(string $class): \ReflectionClass
    {
        if (!\class_exists($class) && !\interface_exists($class) && !\trait_exists($class)) {
            throw ClassNotFoundException::becauseClassDoesNotExist($class);
        }

        return new \ReflectionClass($class);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return \ReflectionClass<T>
     * @throws ClassNotFoundException in case of the class does not exist
     * @throws ClassNotInstantiableException in case of the class is
     *         abstract, an interface, a trait or an enum
     */
    private static function getInstantiableClass(string $class): \ReflectionClass
    {
        $reflection = self::getClass($class);

        if ($reflection->isInterface()) {
            throw ClassNotInstantiableException::becauseClassIsInterface($class);
        }

        if ($reflection->isTrait()) {
            throw ClassNotInstantiableException::becauseClassIsTrait($class);
        }

        if ($reflection->isEnum()) {
            throw ClassNotInstantiableException::becauseClassIsEnum($class);
        }

        if ($reflection->isAbstract()) {
            throw ClassNotInstantiableException::becauseClassIsAbstract($class);
        }

        return $reflection;
    }
}
