<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel\Tests;

use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DebugBar;
use Illuminate\Container\Container;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Mapper\Bridge\Laravel\Debugbar\MapperCollector;
use TypeLang\Mapper\Bridge\Laravel\Exception\InvalidConfigException;
use TypeLang\Mapper\Bridge\Laravel\Tests\Stub\CacheFactoryStub;
use TypeLang\Mapper\Bridge\Laravel\Tests\Stub\DecimalCommaCoercerStub;
use TypeLang\Mapper\Bridge\Laravel\Tests\Stub\ShoutTypeBuilderStub;
use TypeLang\Mapper\Bridge\Laravel\Tests\Stub\UserStub;
use TypeLang\Mapper\Bridge\Laravel\Tests\Stub\WhisperTypeBuilderStub;
use TypeLang\Mapper\DenormalizerInterface;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Exception\TypeDefinitionException;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\MapperInterface;
use TypeLang\Mapper\NormalizerInterface;
use TypeLang\Mapper\Profiler\TraceableMapper;
use TypeLang\Mapper\Type\Builder\TypeBuilderInterface;

final class TypeLangMapperServiceProviderTest extends TestCase
{
    public function testDefaultMapperIsBoundToInterfaces(): void
    {
        $container = self::createContainer();
        $mapper = $container->make('type-lang.mapper');

        self::assertInstanceOf(Mapper::class, $mapper);
        self::assertSame($mapper, $container->make(MapperInterface::class));
        self::assertSame($mapper, $container->make(DenormalizerInterface::class));
        self::assertSame($mapper, $container->make(NormalizerInterface::class));
    }

    public function testNamedMapperInheritsSettingsOfDefaultOne(): void
    {
        $container = self::createContainer([
            'strict' => true,
            'mappers' => ['api' => [], 'loose' => ['strict' => false]],
        ]);

        self::assertFalse($container->make('type-lang.mapper.api')->isDenormalizable('42', 'int'));
        self::assertTrue($container->make('type-lang.mapper.loose')->isDenormalizable('42', 'int'));
    }

    public function testNamedMapperTakesItsExtensionsAndInheritedOnes(): void
    {
        $container = self::createContainer([
            'types' => [ShoutTypeBuilderStub::class],
            'mappers' => ['api' => ['types' => [WhisperTypeBuilderStub::class]]],
        ]);

        $api = $container->make('type-lang.mapper.api');

        self::assertSame('a', $api->denormalize('a', 'shout'));
        self::assertSame('a', $api->denormalize('a', 'whisper'));

        $this->expectException(TypeDefinitionException::class);

        $container->make('type-lang.mapper')->denormalize('a', 'whisper');
    }

    public function testNamingStrategyAndMappingFilesAreApplied(): void
    {
        $container = self::createContainer([
            'naming' => 'snake_case',
            'metadata' => ['paths' => [__DIR__ . '/Stub/config']],
        ]);

        $user = $container->make('type-lang.mapper')->denormalize(['login' => 'root', 'age' => 42], UserStub::class);

        self::assertInstanceOf(UserStub::class, $user);
        self::assertSame('root', $user->userName);
        self::assertSame(42, $user->age);
    }

    public function testCoercerOfNamedMapperReplacesInheritedOne(): void
    {
        $container = self::createContainer([
            'mappers' => ['api' => ['coercers' => ['float' => DecimalCommaCoercerStub::class]]],
        ]);

        self::assertSame(1.5, $container->make('type-lang.mapper.api')->denormalize('1,5', 'float'));
        self::assertFalse($container->make('type-lang.mapper')->isDenormalizable('1,5', 'float'));
    }

    public function testMetadataIsCachedOutOfDebugMode(): void
    {
        $cache = null;
        $container = self::createContainer(
            config: ['metadata' => ['cache' => ['stores' => [['store' => 'redis', 'prefix' => 'hot_']]]]],
            configure: static function (Container $container) use (&$cache): void {
                $cache = $container->make('cache');
            },
        );

        $container->make('type-lang.mapper')->denormalize(['userName' => 'root', 'age' => 42], UserStub::class);

        self::assertInstanceOf(CacheFactoryStub::class, $cache);
        self::assertSame(['redis'], \array_keys($cache->stores));
    }

    public function testMetadataIsNotCachedInDebugMode(): void
    {
        $cache = new CacheFactoryStub();
        $container = self::createContainer(isDebug: true, configure: static function (Container $container) use ($cache): void {
            $container->instance('cache', $cache);
        });

        $container->make('type-lang.mapper')->denormalize(['userName' => 'root', 'age' => 42], UserStub::class);

        self::assertSame([], $cache->stores);
    }

    public function testMappersAreTracedWithDebugbar(): void
    {
        $debugbar = new DebugBar();
        $debugbar->addCollector(new TimeDataCollector());

        $container = self::createContainer(
            config: ['mappers' => ['api' => []]],
            configure: static function (Container $container) use ($debugbar): void {
                $container->instance('debugbar', $debugbar);
            },
        );

        $mapper = $container->make('type-lang.mapper.api');
        $mapper->denormalize('42', 'int');

        self::assertInstanceOf(TraceableMapper::class, $mapper);
        self::assertInstanceOf(TraceableMapper::class, $container->make(MapperInterface::class));

        $collector = $debugbar->getCollector(MapperCollector::NAME);

        self::assertInstanceOf(MapperCollector::class, $collector);
        self::assertSame(1, $collector->collect()['count']);
    }

    public function testProfilerCanBeDisabledWithDebugbar(): void
    {
        $container = self::createContainer(
            config: ['profiler' => false],
            configure: static function (Container $container): void {
                $container->instance('debugbar', new DebugBar());
            },
        );

        self::assertInstanceOf(Mapper::class, $container->make('type-lang.mapper'));
    }

    public function testEnabledProfilerRequiresDebugbar(): void
    {
        $container = self::createContainer(['profiler' => true]);

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Mapper profiler requires the "barryvdh/laravel-debugbar" package');

        $container->make('type-lang.mapper');
    }

    /**
     * @return iterable<non-empty-string, array{array<array-key, mixed>, non-empty-string}>
     */
    public static function invalidConfigDataProvider(): iterable
    {
        yield 'mapper name' => [
            ['mappers' => ['Api' => []]],
            'Mapper name "Api" is invalid, a name matches "/^[a-z][a-z0-9_]*$/" and is not "default"',
        ];

        yield 'default mapper name' => [
            ['mappers' => ['default' => []]],
            'Mapper name "default" is invalid',
        ];
    }

    /**
     * @param array<array-key, mixed> $config
     * @param non-empty-string $message
     */
    #[DataProvider('invalidConfigDataProvider')]
    public function testInvalidMapperNameIsRejected(array $config, string $message): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage($message);

        self::createContainer($config);
    }

    /**
     * @return iterable<non-empty-string, array{array<array-key, mixed>, non-empty-string}>
     */
    public static function invalidSettingDataProvider(): iterable
    {
        yield 'boolean' => [
            ['strict' => 'yes'],
            'Config "type-lang-mapper.strict" must be a boolean, but string given',
        ];

        yield 'integer of named mapper' => [
            ['mappers' => ['api' => ['max_violations' => 0]]],
            'Config "type-lang-mapper.mappers.api.max_violations" must be an integer of at least 1, but int given',
        ];

        yield 'list' => [
            ['types' => 'App\\Type'],
            'Config "type-lang-mapper.types" must be a list, but string given',
        ];

        yield 'coercer type' => [
            ['coercers' => ['decimal' => DecimalCommaCoercerStub::class]],
            'Coercer type "decimal" is not supported, supported types are int, float, string, bool, array',
        ];

        yield 'service' => [
            ['types' => [UserStub::class]],
            \sprintf(
                'Service "%s" must implement "%s", but %s given',
                UserStub::class,
                TypeBuilderInterface::class,
                UserStub::class,
            ),
        ];

        yield 'missing path' => [
            ['metadata' => ['paths' => [__DIR__ . '/missing']]],
            \sprintf('Mapping config path "%s" does not exist', __DIR__ . '/missing'),
        ];
    }

    /**
     * @param array<array-key, mixed> $config
     * @param non-empty-string $message
     */
    #[DataProvider('invalidSettingDataProvider')]
    public function testInvalidSettingIsRejected(array $config, string $message): void
    {
        $container = self::createContainer($config);

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage($message);

        $container->make(\array_key_exists('mappers', $config) ? 'type-lang.mapper.api' : 'type-lang.mapper');
    }
}
