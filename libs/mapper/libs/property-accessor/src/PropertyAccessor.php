<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor;

/**
 * Accessor that is bound to an object by copying.
 *
 * @template T of object
 *
 * @template-implements PropertyAccessorInterface<T>
 */
abstract class PropertyAccessor implements PropertyAccessorInterface
{
    /**
     * @var T
     */
    protected object $object;

    /**
     * Returns a copy of the accessor bound to the given object.
     *
     * @template TArgObject of object
     * @param TArgObject $object
     * @return static<TArgObject>
     */
    public function withContext(object $object): static
    {
        $self = clone $this;
        // @phpstan-ignore-next-line the copy is bound to TArgObject
        $self->object = $object;

        // @phpstan-ignore-next-line the copy is bound to TArgObject
        return $self;
    }
}
