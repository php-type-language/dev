<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Provider;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;

/**
 * Stores metadata provided by the delegate in a PSR-16 cache.
 */
final class Psr16MetadataProvider extends CacheMetadataProvider
{
    /**
     * @param string $prefix prefix of the cache keys, a new prefix discards
     *        the cached metadata
     */
    public function __construct(
        MetadataProviderInterface $delegate,
        private readonly CacheInterface $cache,
        string $prefix = self::DEFAULT_PREFIX,
    ) {
        parent::__construct($delegate, $prefix);
    }

    /**
     * @throws InvalidArgumentException in case of the cache rejects the key
     */
    protected function fetch(string $key): mixed
    {
        return $this->cache->get($key);
    }

    /**
     * @throws InvalidArgumentException in case of the cache rejects the key
     */
    protected function store(string $key, ClassMetadata $metadata): void
    {
        $this->cache->set($key, $metadata);
    }
}
