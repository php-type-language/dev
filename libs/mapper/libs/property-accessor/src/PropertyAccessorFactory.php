<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor;

/**
 * Creates accessors bound to an object, the implementation is chosen by the
 * class of the object and the PHP version.
 */
final class PropertyAccessorFactory
{
    /**
     * @var array<class-string, PropertyAccessor<object>>
     */
    private array $prototypes = [];

    /**
     * @template T of object
     * @param T $object
     * @return PropertyAccessorInterface<T>
     */
    public function create(object $object): PropertyAccessorInterface
    {
        return ($this->prototypes[$object::class] ??= self::createPrototype($object::class))
            ->withContext($object);
    }

    /**
     * @template TArgSubject of object
     * @param class-string<TArgSubject> $class
     * @return PropertyAccessor<TArgSubject>
     */
    private static function createPrototype(string $class): PropertyAccessor
    {
        if ($class === \stdClass::class) {
            /** @var DynamicPropertyAccessor<TArgSubject> */
            return new DynamicPropertyAccessor();
        }

        $reflection = new \ReflectionClass($class);

        if (\PHP_VERSION_ID >= 80400) {
            return new ModernReflectionPropertyAccessor($reflection);
        }

        return new ReflectionPropertyAccessor($reflection);
    }
}
