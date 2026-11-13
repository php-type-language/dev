<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Tests\Provider;

use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use TypeLang\Mapper\Mapping\Exception\MissingDependencyException;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;
use TypeLang\Mapper\Mapping\Tests\Stub\ArrayCache;
use TypeLang\Mapper\Mapping\Tests\Stub\ArrayCachePool;
use TypeLang\Mapper\Mapping\Tests\Stub\AttributeStub;
use TypeLang\Mapper\Mapping\Tests\Stub\DocBlockStub;
use TypeLang\Mapper\Mapping\Tests\Stub\DuplicateNameStub;
use TypeLang\Mapper\Mapping\Tests\Stub\UserStub;
use TypeLang\Mapper\Mapping\Tests\TestCase;
use TypeLang\Mapper\Naming\NamingStrategy;

final class MetadataProviderFactoryTest extends TestCase
{
    public function testDefaultStackTakesHigherReaderForEachValue(): void
    {
        $config = new ArrayMetadataReader([
            AttributeStub::class => [
                'properties' => [
                    'id' => ['default' => 5],
                    'value' => 'string',
                    'accounts' => ['write_type' => 'array'],
                ],
            ],
        ]);

        $metadata = MetadataProviderFactory::create(readers: [$config])
            ->read(new \ReflectionClass(AttributeStub::class));
        $path = '$[\'' . AttributeStub::class . '\'].properties';

        $id = self::getProperty($metadata, 'id');
        self::assertSame(5, $id->default?->value);
        self::assertSame($path . '.id.default', $id->default->source->path);

        $value = self::getProperty($metadata, 'value');
        self::assertType('string', $value->read);
        self::assertType('string', $value->write);
        self::assertSame($path . '.value', $value->read->source->path);

        $accounts = self::getProperty($metadata, 'accounts');
        self::assertType('list<' . UserStub::class . '>', $accounts->read);
        self::assertType('array', $accounts->write);
        self::assertNull($accounts->read->source->path);
        self::assertSame($path . '.accounts.write_type', $accounts->write->source->path);

        $count = self::getProperty($metadata, 'count');
        self::assertType('positive-int', $count->read);
        self::assertType('positive-int', $count->write);
        self::assertSame(1, $count->default?->value);
        self::assertNull($count->default->source->path);
    }

    public function testStrategyNamesPropertiesWithoutExternalName(): void
    {
        $metadata = MetadataProviderFactory::create(strategy: NamingStrategy::SnakeCase)
            ->read(new \ReflectionClass(AttributeStub::class));

        self::assertSame('user_id', self::getProperty($metadata, 'id')->getOutputName());

        $metadata = MetadataProviderFactory::create(strategy: NamingStrategy::SnakeCase)
            ->read(new \ReflectionClass(UserStub::class));

        self::assertSame('display_name', self::getProperty($metadata, 'displayName')->getOutputName());
    }

    public function testDefaultStackChecksFinalMetadata(): void
    {
        $errors = self::getErrors(static fn() => MetadataProviderFactory::create()
            ->read(new \ReflectionClass(DuplicateNameStub::class)));

        self::assertCount(1, $errors);
    }

    public function testDefaultStackKeepsMetadataInMemory(): void
    {
        $provider = MetadataProviderFactory::create();

        $first = $provider->read(new \ReflectionClass(UserStub::class));
        $second = $provider->read(new \ReflectionClass(UserStub::class));

        self::assertSame($first, $second);
    }

    public function testDefaultStackStoresMetadataInPsrCache(): void
    {
        $pool = new ArrayCachePool();
        $cache = new ArrayCache();

        MetadataProviderFactory::create(cache: $pool, prefix: 'app_')
            ->read(new \ReflectionClass(UserStub::class));
        MetadataProviderFactory::create(cache: $cache, prefix: 'app_')
            ->read(new \ReflectionClass(UserStub::class));

        self::assertStringStartsWith('app_', (string) \array_key_first($pool->values));
        self::assertStringStartsWith('app_', (string) \array_key_first($cache->values));
    }

    public function testGivenReadersAreChainedInGivenOrder(): void
    {
        $metadata = MetadataProviderFactory::create(readers: [
            new ArrayMetadataReader([UserStub::class => ['properties' => ['id' => 'positive-int']]]),
            new ArrayMetadataReader([UserStub::class => ['properties' => ['id' => 'int']]]),
        ])->read(new \ReflectionClass(UserStub::class));

        self::assertType('positive-int', self::getProperty($metadata, 'id')->read);
    }

    #[RunInSeparateProcess]
    public function testDefaultStackWithoutPhpDocPackageSkipsDocBlocks(): void
    {
        self::hidePhpDocPackage();

        $metadata = MetadataProviderFactory::create()->read(new \ReflectionClass(DocBlockStub::class));

        self::assertType('array', self::getProperty($metadata, 'accounts')->read);

        $this->expectException(MissingDependencyException::class);

        new PhpDocMetadataReader();
    }

    /**
     * Makes the classes of the `type-lang/phpdoc` package unloadable in the
     * current process.
     */
    private static function hidePhpDocPackage(): void
    {
        $loaders = \spl_autoload_functions();

        foreach ($loaders as $loader) {
            \spl_autoload_unregister($loader);
        }

        \spl_autoload_register(static function (string $class) use ($loaders): void {
            if (\str_starts_with($class, 'TypeLang\\PhpDoc\\')) {
                return;
            }

            foreach ($loaders as $loader) {
                $loader($class);
            }
        });

        self::assertFalse(PhpDocMetadataReader::isAvailable());
    }
}
