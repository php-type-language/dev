<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Provider;

use TypeLang\Mapper\Mapping\Exception\InvalidMetadataException;
use TypeLang\Mapper\Mapping\Exception\ReaderException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;

/**
 * Provides metadata of classes.
 */
interface MetadataProviderInterface
{
    /**
     * @template T of object
     * @param \ReflectionClass<T> $class
     * @return ClassMetadata<T>
     * @throws InvalidMetadataException in case of the metadata has wrong
     *         values
     * @throws ReaderException in case of the metadata cannot be
     *         provided
     */
    public function read(\ReflectionClass $class): ClassMetadata;
}
