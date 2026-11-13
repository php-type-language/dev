<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Provider;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use TypeLang\Mapper\Mapping\Exception\ReaderException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;

/**
 * Stores metadata provided by the delegate in a PSR-6 cache pool.
 */
final class Psr6MetadataProvider implements MetadataProviderInterface
{
    public function __construct(
        private readonly MetadataProviderInterface $delegate,
        private readonly CacheItemPoolInterface $pool,
        /**
         * Prefix of the cache keys, a new prefix discards the cached metadata
         */
        private readonly string $prefix = 'tlm_',
    ) {}

    /**
     * @template T of object
     * @param \ReflectionClass<T> $class
     * @return ClassMetadata<T>
     * @throws InvalidArgumentException in case of the pool rejects the key
     * @throws ReaderException in case of the delegate cannot
     *         provide the metadata
     */
    public function read(\ReflectionClass $class): ClassMetadata
    {
        $item = $this->pool->getItem($this->prefix . \hash('xxh128', $class->name));

        $metadata = $item->get();

        if ($metadata instanceof ClassMetadata && $metadata->name === $class->name) {
            /** @var ClassMetadata<T> */
            return $metadata;
        }

        $metadata = $this->delegate->read($class);

        $this->pool->save($item->set($metadata));

        return $metadata;
    }
}
