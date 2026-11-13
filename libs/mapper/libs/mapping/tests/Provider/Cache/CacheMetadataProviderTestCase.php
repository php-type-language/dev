<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Tests\Provider\Cache;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Mapping\Provider\MetadataProviderInterface;
use TypeLang\Mapper\Mapping\Tests\Stub\ArrayCache;
use TypeLang\Mapper\Mapping\Tests\Stub\ArrayCachePool;
use TypeLang\Mapper\Mapping\Tests\Stub\CountingMetadataProvider;
use TypeLang\Mapper\Mapping\Tests\Stub\ReaderExceptionStub;
use TypeLang\Mapper\Mapping\Tests\TestCase;

/**
 * Behavior shared by the providers storing metadata in a PSR cache.
 */
abstract class CacheMetadataProviderTestCase extends TestCase
{
    abstract protected function createCache(): ArrayCachePool|ArrayCache;

    abstract protected function createProvider(
        MetadataProviderInterface $delegate,
        ArrayCachePool|ArrayCache $cache,
        string $prefix = 'tlm_',
    ): MetadataProviderInterface;

    public function testMissCallsDelegateAndStoresMetadata(): void
    {
        $delegate = new CountingMetadataProvider();
        $cache = $this->createCache();

        $metadata = $this->createProvider($delegate, $cache)
            ->read(new \ReflectionClass(\stdClass::class));

        self::assertSame(1, $delegate->calls);
        self::assertSame(\stdClass::class, $metadata->name);
        self::assertCount(1, $cache->values);
    }

    public function testHitDoesNotCallDelegate(): void
    {
        $class = new \ReflectionClass(\stdClass::class);
        $cache = $this->createCache();

        $this->createProvider(new CountingMetadataProvider(), $cache)
            ->read($class);

        $delegate = new CountingMetadataProvider();

        $metadata = $this->createProvider($delegate, $cache)
            ->read($class);

        self::assertSame(0, $delegate->calls);
        self::assertEquals(new ClassMetadata(\stdClass::class), $metadata);
    }

    public function testKeyIsValidForPsrCache(): void
    {
        $cache = $this->createCache();

        $this->createProvider(new CountingMetadataProvider(), $cache)
            ->read(new \ReflectionClass(\ArrayObject::class));

        self::assertMatchesRegularExpression('/^tlm_[A-Za-z0-9_.]+$/', (string) \array_key_first($cache->values));
    }

    public function testDifferentPrefixesDoNotShareMetadata(): void
    {
        $class = new \ReflectionClass(\stdClass::class);
        $cache = $this->createCache();
        $first = new CountingMetadataProvider();
        $second = new CountingMetadataProvider();

        $this->createProvider($first, $cache, 'first_')
            ->read($class);
        $this->createProvider($second, $cache, 'second_')
            ->read($class);

        self::assertSame(1, $first->calls);
        self::assertSame(1, $second->calls);
        self::assertCount(2, $cache->values);
    }

    /**
     * @return iterable<non-empty-string, array{mixed}>
     */
    public static function garbageDataProvider(): iterable
    {
        yield 'string' => ['metadata'];
        yield 'false' => [false];
        yield 'object of another class' => [new \ArrayObject()];
        yield 'metadata of another class' => [new ClassMetadata(\ArrayObject::class)];
    }

    #[DataProvider('garbageDataProvider')]
    public function testGarbageIsMissAndIsOverwritten(mixed $garbage): void
    {
        $class = new \ReflectionClass(\stdClass::class);
        $cache = $this->createCache();

        $this->createProvider(new CountingMetadataProvider(), $cache)
            ->read($class);

        foreach (\array_keys($cache->values) as $key) {
            $cache->values[$key] = \serialize($garbage);
        }

        $delegate = new CountingMetadataProvider();
        $provider = $this->createProvider($delegate, $cache);

        $metadata = $provider->read($class);
        $provider->read($class);

        self::assertSame(1, $delegate->calls);
        self::assertSame(\stdClass::class, $metadata->name);
    }

    public function testDelegateExceptionIsNotCached(): void
    {
        $exception = new ReaderExceptionStub();
        $cache = $this->createCache();

        try {
            $this->createProvider(new CountingMetadataProvider($exception), $cache)
                ->read(new \ReflectionClass(\stdClass::class));

            self::fail('The exception of the delegate is expected');
        } catch (ReaderExceptionStub $e) {
            self::assertSame($exception, $e);
        }

        self::assertSame([], $cache->values);
    }
}
