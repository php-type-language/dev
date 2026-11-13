<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Provider;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use TypeLang\Mapper\Mapping\Exception\ReaderException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;

/**
 * Stores metadata provided by the delegate in a PSR-16 cache.
 */
final class Psr16MetadataProvider implements MetadataProviderInterface
{
    public function __construct(
        private readonly MetadataProviderInterface $delegate,
        private readonly CacheInterface $cache,
        /**
         * Prefix of the cache keys, a new prefix discards the cached metadata
         */
        private readonly string $prefix = 'tlm_',
    ) {}

    /**
     * @template T of object
     * @param \ReflectionClass<T> $class
     * @return ClassMetadata<T>
     * @throws InvalidArgumentException in case of the cache rejects the key
     * @throws ReaderException in case of the delegate cannot
     *         provide the metadata
     */
    public function read(\ReflectionClass $class): ClassMetadata
    {
        $key = $this->prefix . \hash('xxh128', $class->name);

        $metadata = $this->cache->get($key);

        if ($metadata instanceof ClassMetadata && $metadata->name === $class->name) {
            /** @var ClassMetadata<T> */
            return $metadata;
        }

        $metadata = $this->delegate->read($class);

        $this->cache->set($key, $metadata);

        return $metadata;
    }
}
