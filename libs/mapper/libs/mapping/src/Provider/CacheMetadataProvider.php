<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Provider;

use TypeLang\Mapper\Mapping\Exception\ReaderException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;

/**
 * Stores metadata provided by the delegate in a PSR cache. The key of the
 * metadata of a class is the prefix, {@see ClassMetadata::FORMAT_VERSION}
 * and the hash of the class name, a value other than the metadata of the
 * class is a miss.
 */
abstract class CacheMetadataProvider implements MetadataProviderInterface
{
    /**
     * Prefix of the cache keys used in case of the user sets none
     */
    public const DEFAULT_PREFIX = 'tlm_';

    public function __construct(
        private readonly MetadataProviderInterface $delegate,
        /**
         * Prefix of the cache keys, a new prefix discards the cached metadata
         */
        private readonly string $prefix = self::DEFAULT_PREFIX,
    ) {}

    /**
     * @template T of object
     * @param \ReflectionClass<T> $class
     * @return ClassMetadata<T>
     * @throws ReaderException in case of the delegate cannot provide the
     *         metadata
     */
    final public function read(\ReflectionClass $class): ClassMetadata
    {
        $key = $this->prefix . 'v' . ClassMetadata::FORMAT_VERSION . '_' . \hash('xxh128', $class->name);

        $metadata = $this->fetch($key);

        if ($metadata instanceof ClassMetadata && $metadata->name === $class->name) {
            /** @var ClassMetadata<T> */
            return $metadata;
        }

        $metadata = $this->delegate->read($class);

        $this->store($key, $metadata);

        return $metadata;
    }

    /**
     * Returns the value stored by the key, {@see null} in case of a miss.
     *
     * @param non-empty-string $key valid for PSR-6 and PSR-16 in case of the
     *        prefix is
     */
    abstract protected function fetch(string $key): mixed;

    /**
     * @param non-empty-string $key valid for PSR-6 and PSR-16 in case of the
     *        prefix is
     * @param ClassMetadata<object> $metadata
     */
    abstract protected function store(string $key, ClassMetadata $metadata): void;
}
