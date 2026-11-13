<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Provider;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;

/**
 * Stores metadata provided by the delegate in a PSR-6 cache pool.
 */
final class Psr6MetadataProvider extends CacheMetadataProvider
{
    /**
     * @param string $prefix prefix of the cache keys, a new prefix discards
     *        the cached metadata
     */
    public function __construct(
        MetadataProviderInterface $delegate,
        private readonly CacheItemPoolInterface $pool,
        string $prefix = self::DEFAULT_PREFIX,
    ) {
        parent::__construct($delegate, $prefix);
    }

    /**
     * @throws InvalidArgumentException in case of the pool rejects the key
     */
    protected function fetch(string $key): mixed
    {
        return $this->pool->getItem($key)->get();
    }

    /**
     * @throws InvalidArgumentException in case of the pool rejects the key
     */
    protected function store(string $key, ClassMetadata $metadata): void
    {
        $this->pool->save($this->pool->getItem($key)->set($metadata));
    }
}
