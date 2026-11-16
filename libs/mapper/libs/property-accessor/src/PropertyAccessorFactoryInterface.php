<?php

declare(strict_types=1);

namespace TypeLang\Mapper\PropertyAccessor;

use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotFoundException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotReadableException;
use TypeLang\Mapper\PropertyAccessor\Exception\PropertyNotWritableException;

/**
 * Strategy of reading and writing one property of any object of its class,
 * chosen once by the declaration of the property. A property that cannot be
 * accessed is rejected on creation of its reader or writer.
 */
interface PropertyAccessorFactoryInterface
{
    /**
     * @return PropertyReaderInterface<object, mixed>
     * @throws PropertyNotFoundException in case of the property is static
     * @throws PropertyNotReadableException in case of the property is
     *         virtual without a get hook
     */
    public function createReader(\ReflectionProperty $property): PropertyReaderInterface;

    /**
     * @return PropertyWriterInterface<object, mixed>
     * @throws PropertyNotFoundException in case of the property is static
     * @throws PropertyNotWritableException in case of the property is
     *         virtual without a set hook
     */
    public function createWriter(\ReflectionProperty $property): PropertyWriterInterface;
}
