<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel\Tests;

use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DebugBar;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Laravel\Telescope\Contracts\EntriesRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Mapper\Bridge\Laravel\Debugbar\MapperCollector;
use TypeLang\Mapper\Bridge\Laravel\Exception\InvalidConfigException;
use TypeLang\Mapper\Bridge\Laravel\ProfilerChain;
use TypeLang\Mapper\Bridge\Laravel\Telescope\MapperRecorder;
use TypeLang\Mapper\Bridge\Laravel\Tests\Stub\CacheFactoryStub;
use TypeLang\Mapper\Bridge\Laravel\Tests\Stub\DecimalCommaCoercerStub;
use TypeLang\Mapper\Bridge\Laravel\Tests\Stub\ShoutingFormatterStub;
use TypeLang\Mapper\Bridge\Laravel\Tests\Stub\ShoutTypeBuilderStub;
use TypeLang\Mapper\Bridge\Laravel\Tests\Stub\UserStub;
use TypeLang\Mapper\Bridge\Laravel\Tests\Stub\WhisperTypeBuilderStub;
use TypeLang\Mapper\DenormalizerInterface;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Exception\TypeDefinitionException;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\MapperInterface;
use TypeLang\Mapper\NormalizerInterface;
use TypeLang\Mapper\Profiler\ProfilerInterface;
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

    public function testFormatterOfDefaultMapperIsInherited(): void
    {
        $container = self::createContainer([
            'formatters' => ['denormalization' => ShoutingFormatterStub::class],
            'mappers' => ['api' => []],
        ]);

        foreach (['type-lang.mapper', 'type-lang.mapper.api'] as $id) {
            try {
                $container->make($id)->denormalize('x', 'int');
                self::fail('The value must be rejected');
            } catch (InvalidValueException $e) {
                self::assertSame('$: INVALID INVALID_TYPE', $e->getMessage());
            }
        }
    }

    public function testFormatterOfDirectionKeepsTheOtherDirection(): void
    {
        $container = self::createContainer([
            'formatters' => ['normalization' => ShoutingFormatterStub::class],
        ]);

        try {
            $container->make('type-lang.mapper')->normalize('x', 'int');
            self::fail('The value must be rejected');
        } catch (InvalidValueException $e) {
            self::assertSame('$: INVALID INVALID_TYPE', $e->getMessage());
        }

        $this->expectExceptionMessage('$: Value must be of type int, but "x" given');

        $container->make('type-lang.mapper')->denormalize('x', 'int');
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
            config: ['profiler' => ['debugbar' => false]],
            configure: static function (Container $container): void {
                $container->instance('debugbar', new DebugBar());
            },
        );

        self::assertInstanceOf(Mapper::class, $container->make('type-lang.mapper'));
    }

    public function testMappersAreTracedWithTelescope(): void
    {
        $container = self::createContainer(configure: static function (Container $container): void {
            self::addTelescope($container, isEnabled: true);
        });

        $mapper = $container->make('type-lang.mapper');

        self::assertInstanceOf(TraceableMapper::class, $mapper);
        self::assertInstanceOf(MapperRecorder::class, self::getProfiler($mapper));
    }

    public function testDisabledTelescopeIsNotTraced(): void
    {
        $container = self::createContainer(configure: static function (Container $container): void {
            self::addTelescope($container, isEnabled: false);
        });

        self::assertInstanceOf(Mapper::class, $container->make('type-lang.mapper'));
    }

    public function testDebugbarAndTelescopeAreTracedTogether(): void
    {
        $debugbar = new DebugBar();

        $container = self::createContainer(configure: static function (Container $container) use ($debugbar): void {
            $container->instance('debugbar', $debugbar);
            self::addTelescope($container, isEnabled: true);
        });

        $mapper = $container->make('type-lang.mapper');
        $mapper->denormalize('42', 'int');

        self::assertInstanceOf(ProfilerChain::class, self::getProfiler($mapper));
        self::assertSame(1, $debugbar->getCollector(MapperCollector::NAME)->collect()['count']);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, non-empty-string}>
     */
    public static function profilerWithoutToolDataProvider(): iterable
    {
        yield 'debugbar' => ['debugbar', 'barryvdh/laravel-debugbar'];
        yield 'telescope' => ['telescope', 'laravel/telescope'];
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $package
     */
    #[DataProvider('profilerWithoutToolDataProvider')]
    public function testEnabledProfilerRequiresItsTool(string $name, string $package): void
    {
        $container = self::createContainer(['profiler' => [$name => true]]);

        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage(\sprintf(
            'Config "type-lang-mapper.profiler.%s" requires the "%s" package',
            $name,
            $package,
        ));

        $container->make('type-lang.mapper');
    }

    private static function addTelescope(Container $container, bool $isEnabled): void
    {
        $container->bind(EntriesRepository::class, static function (): never {
            throw new \LogicException('Telescope storage is not available in tests');
        });

        $config = $container->make('config');

        \assert($config instanceof Repository);

        $config->set('telescope.enabled', $isEnabled);
    }

    private static function getProfiler(TraceableMapper $mapper): ProfilerInterface
    {
        $result = (new \ReflectionProperty($mapper, 'profiler'))->getValue($mapper);

        \assert($result instanceof ProfilerInterface);

        return $result;
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

        yield 'profiler flag' => [
            ['profiler' => ['telescope' => 'yes']],
            'Config "type-lang-mapper.profiler.telescope" must be a boolean, but string given',
        ];

        yield 'list' => [
            ['types' => 'App\\Type'],
            'Config "type-lang-mapper.types" must be a list, but string given',
        ];

        yield 'coercer type' => [
            ['coercers' => ['decimal' => DecimalCommaCoercerStub::class]],
            'Config "type-lang-mapper.coercers.decimal" is not supported, supported keys are int, float, '
                . 'string, bool, array',
        ];

        yield 'formatter direction' => [
            ['formatters' => ['input' => ShoutingFormatterStub::class]],
            'Config "type-lang-mapper.formatters.input" is not supported, supported keys are denormalization, '
                . 'normalization',
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
