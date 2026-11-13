<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Provider;

use Psr\Cache\CacheException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use TypeLang\Mapper\Mapping\Exception\InvalidCacheKeyException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;

/**
 * Stores metadata provided by the delegate in a PSR-6 cache pool. A failure
 * of the pool is a miss on reading and is skipped on writing.
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

    protected function remember(string $key, string $class, \Closure $read): ClassMetadata
    {
        try {
            $item = $this->pool->getItem($key);
        } catch (InvalidArgumentException $e) {
            throw InvalidCacheKeyException::becauseKeyIsRejected($key, $e);
            // @phpstan-ignore-next-line PSR-6 allows CacheException for any failure, the interface declares less
        } catch (CacheException) {
            return $read();
        }

        $metadata = $item->get();

        if (self::isMetadataOf($metadata, $class)) {
            return $metadata;
        }

        $metadata = $read();

        try {
            $this->pool->save($item->set($metadata));
        } catch (CacheException) {
            return $metadata;
        }

        return $metadata;
    }
}
