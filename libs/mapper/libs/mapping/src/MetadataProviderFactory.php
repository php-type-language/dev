<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping;

use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use TypeLang\Mapper\Mapping\Provider\CacheMetadataProvider;
use TypeLang\Mapper\Mapping\Provider\InMemoryMetadataProvider;
use TypeLang\Mapper\Mapping\Provider\MetadataProviderInterface;
use TypeLang\Mapper\Mapping\Provider\Psr16MetadataProvider;
use TypeLang\Mapper\Mapping\Provider\Psr6MetadataProvider;
use TypeLang\Mapper\Mapping\Provider\ValidatingMetadataProvider;
use TypeLang\Mapper\Mapping\Reader\AttributeMetadataReader;
use TypeLang\Mapper\Mapping\Reader\ChainMetadataReader;
use TypeLang\Mapper\Mapping\Reader\MetadataReaderInterface;
use TypeLang\Mapper\Mapping\Reader\NamingStrategyMetadataReader;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;
use TypeLang\Mapper\Mapping\Reader\ReflectionMetadataReader;
use TypeLang\Mapper\Naming\NamingStrategyInterface;

/**
 * Creates the default provider stack.
 */
final class MetadataProviderFactory
{
    /**
     * Creates the stack from the outer provider to the inner one: in-memory
     * cache, PSR cache (if any), final checks and the chain of the naming
     * strategy (if any), the given readers, attributes, phpdoc (if the
     * `type-lang/phpdoc` package is installed), reflection.
     *
     * @param list<MetadataReaderInterface> $readers readers above the
     *        default ones, from the highest priority to the lowest one
     * @param string $prefix prefix of the keys of the cache
     */
    public static function create(
        array $readers = [],
        ?NamingStrategyInterface $strategy = null,
        CacheItemPoolInterface|CacheInterface|null $cache = null,
        string $prefix = CacheMetadataProvider::DEFAULT_PREFIX,
    ): MetadataProviderInterface {
        $result = [];

        if ($strategy !== null) {
            $result[] = new NamingStrategyMetadataReader($strategy);
        }

        $provider = self::readersToProvider([
            ...$result,
            ...$readers,
            ...self::getDefaultReaders(),
        ]);

        return new InMemoryMetadataProvider(
            self::extendProviderByCache($provider, $cache, $prefix),
        );
    }

    /**
     * @param list<MetadataReaderInterface> $readers
     */
    private static function readersToProvider(array $readers): MetadataProviderInterface
    {
        return new ValidatingMetadataProvider(
            new ChainMetadataReader($readers),
        );
    }

    /**
     * @return non-empty-list<MetadataReaderInterface>
     */
    private static function getDefaultReaders(): array
    {
        $result = [
            new AttributeMetadataReader(),
        ];

        if (PhpDocMetadataReader::isAvailable()) {
            $result[] = new PhpDocMetadataReader();
        }

        $result[] = new ReflectionMetadataReader();

        return $result;
    }

    private static function extendProviderByCache(
        MetadataProviderInterface $provider,
        CacheItemPoolInterface|CacheInterface|null $cache,
        string $prefix,
    ): MetadataProviderInterface {
        if ($cache instanceof CacheItemPoolInterface) {
            return new Psr6MetadataProvider($provider, $cache, $prefix);
        }

        if ($cache instanceof CacheInterface) {
            return new Psr16MetadataProvider($provider, $cache, $prefix);
        }

        return $provider;
    }
}
