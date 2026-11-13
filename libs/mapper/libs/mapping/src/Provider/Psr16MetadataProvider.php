<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Provider;

use Psr\SimpleCache\CacheException;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use TypeLang\Mapper\Mapping\Exception\InvalidCacheKeyException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;

/**
 * Stores metadata provided by the delegate in a PSR-16 cache. A failure of
 * the cache is a miss on reading and is skipped on writing.
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

    protected function remember(string $key, string $class, \Closure $read): ClassMetadata
    {
        try {
            $metadata = $this->cache->get($key);
        } catch (InvalidArgumentException $e) {
            throw InvalidCacheKeyException::becauseKeyIsRejected($key, $e);
            // @phpstan-ignore-next-line PSR-16 allows CacheException for any failure, the interface declares less
        } catch (CacheException) {
            return $read();
        }

        if (self::isMetadataOf($metadata, $class)) {
            return $metadata;
        }

        $metadata = $read();

        try {
            $this->cache->set($key, $metadata);
        } catch (CacheException) {
            return $metadata;
        }

        return $metadata;
    }
}
