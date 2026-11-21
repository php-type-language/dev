<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel;

use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DebugBar;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Contracts\EntriesRepository;
use Laravel\Telescope\Telescope;
use TypeLang\Mapper\Bridge\Laravel\Debugbar\MapperCollector;
use TypeLang\Mapper\Bridge\Laravel\Exception\InvalidConfigException;
use TypeLang\Mapper\Bridge\Laravel\Telescope\MapperRecorder;
use TypeLang\Mapper\DenormalizerInterface;
use TypeLang\Mapper\MapperInterface;
use TypeLang\Mapper\NormalizerInterface;
use TypeLang\Mapper\Profiler\ProfilerInterface;

/**
 * Registers the default mapper (`type-lang.mapper`, also bound to the
 * mapper interfaces) and the named mappers extending it
 * (`type-lang.mapper.<name>`) from the `type-lang-mapper` config.
 */
final class TypeLangMapperServiceProvider extends ServiceProvider
{
    public const MAPPER_ID = 'type-lang.mapper';

    public const CONFIG_KEY = 'type-lang-mapper';

    private const CONFIG_PATH = __DIR__ . '/../config/type-lang-mapper.php';

    /**
     * Contracts of a mapper bound to the default mapper
     */
    private const MAPPER_INTERFACES = [
        MapperInterface::class,
        DenormalizerInterface::class,
        NormalizerInterface::class,
    ];

    /**
     * Returns the id of the mapper in the container.
     *
     * @param non-empty-string $name
     * @return non-empty-string
     */
    public static function getMapperId(string $name): string
    {
        if ($name === MapperFactory::DEFAULT_NAME) {
            return self::MAPPER_ID;
        }

        return self::MAPPER_ID . '.' . $name;
    }

    /**
     * @throws InvalidConfigException in case of a name of a mapper is invalid
     */
    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, self::CONFIG_KEY);

        $this->registerMapper(MapperFactory::DEFAULT_NAME);

        foreach (self::MAPPER_INTERFACES as $interface) {
            $this->app->alias(self::MAPPER_ID, $interface);
        }

        foreach (MapperFactory::getMapperNames(self::readConfig($this->app)) as $name) {
            $this->registerMapper($name);
        }
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes(
                [self::CONFIG_PATH => $this->app->configPath(self::CONFIG_KEY . '.php')],
                self::CONFIG_KEY . '-config',
            );
        }
    }

    /**
     * @param non-empty-string $name
     */
    private function registerMapper(string $name): void
    {
        $this->app->singleton(
            self::getMapperId($name),
            static fn(Container $app): MapperInterface => self::createFactory($app)->create($name),
        );
    }

    /**
     * @throws InvalidConfigException in case of the config is invalid
     */
    private static function createFactory(Container $app): MapperFactory
    {
        return new MapperFactory(
            container: $app,
            config: self::readConfig($app),
            isDebug: self::getConfigRepository($app)->get('app.debug') === true,
            profiler: self::findProfiler($app),
        );
    }

    /**
     * Returns the profilers of the Debugbar and of Telescope enabled in the
     * config, by default each one together with its tool.
     *
     * @throws InvalidConfigException in case of the profiler config is
     *         invalid or a profiler is enabled without its tool
     */
    private static function findProfiler(Container $app): ?ProfilerInterface
    {
        $config = self::readConfig($app)['profiler'] ?? [];

        if (!\is_array($config)) {
            throw InvalidConfigException::becauseValueIsInvalid(self::CONFIG_KEY . '.profiler', 'an array', $config);
        }

        $profilers = [];

        $debugbar = self::findDebugbarCollector($app, self::readProfilerFlag($config, 'debugbar'));

        if ($debugbar !== null) {
            $profilers[] = $debugbar;
        }

        $telescope = self::findTelescopeRecorder($app, self::readProfilerFlag($config, 'telescope'));

        if ($telescope !== null) {
            $profilers[] = $telescope;
        }

        return match (\count($profilers)) {
            0 => null,
            1 => $profilers[0],
            default => new ProfilerChain($profilers),
        };
    }

    /**
     * @param array<array-key, mixed> $config
     * @param non-empty-string $name
     * @throws InvalidConfigException in case of the flag is not a boolean
     */
    private static function readProfilerFlag(array $config, string $name): ?bool
    {
        $result = $config[$name] ?? null;

        if ($result !== null && !\is_bool($result)) {
            throw InvalidConfigException::becauseValueIsInvalid(
                key: self::CONFIG_KEY . '.profiler.' . $name,
                expected: 'a boolean',
                value: $result,
            );
        }

        return $result;
    }

    /**
     * @param bool|null $isEnabled the flag of the config, {@see null} enables
     *        the profiler together with the Debugbar
     * @throws InvalidConfigException in case of the profiler is enabled
     *         without the Debugbar
     */
    private static function findDebugbarCollector(Container $app, ?bool $isEnabled): ?MapperCollector
    {
        if ($isEnabled === false) {
            return null;
        }

        $debugbar = $app->bound('debugbar') ? $app->make('debugbar') : null;

        if (!$debugbar instanceof DebugBar) {
            if ($isEnabled === true) {
                throw InvalidConfigException::becauseProfilerRequiresPackage(
                    key: self::CONFIG_KEY . '.profiler.debugbar',
                    package: 'barryvdh/laravel-debugbar',
                );
            }

            return null;
        }

        if ($isEnabled === null && \method_exists($debugbar, 'isEnabled') && $debugbar->isEnabled() !== true) {
            return null;
        }

        if (!$debugbar->hasCollector(MapperCollector::NAME)) {
            $timeline = $debugbar->hasCollector('time') ? $debugbar->getCollector('time') : null;

            $debugbar->addCollector(new MapperCollector(
                $timeline instanceof TimeDataCollector ? $timeline : null,
            ));
        }

        $result = $debugbar->getCollector(MapperCollector::NAME);

        \assert($result instanceof MapperCollector);

        return $result;
    }

    /**
     * @param bool|null $isEnabled the flag of the config, {@see null} enables
     *        the profiler together with Telescope
     * @throws InvalidConfigException in case of the profiler is enabled
     *         without Telescope
     */
    private static function findTelescopeRecorder(Container $app, ?bool $isEnabled): ?MapperRecorder
    {
        if ($isEnabled === false) {
            return null;
        }

        if (!\class_exists(Telescope::class) || !$app->bound(EntriesRepository::class)) {
            if ($isEnabled === true) {
                throw InvalidConfigException::becauseProfilerRequiresPackage(
                    key: self::CONFIG_KEY . '.profiler.telescope',
                    package: 'laravel/telescope',
                );
            }

            return null;
        }

        $isTelescopeEnabled = \filter_var(
            self::getConfigRepository($app)->get('telescope.enabled'),
            \FILTER_VALIDATE_BOOL,
        );

        if ($isEnabled === null && !$isTelescopeEnabled) {
            return null;
        }

        return new MapperRecorder();
    }

    /**
     * @return array<array-key, mixed>
     * @throws InvalidConfigException in case of the config is not an array
     */
    private static function readConfig(Container $app): array
    {
        $result = self::getConfigRepository($app)->get(self::CONFIG_KEY, []);

        if (!\is_array($result)) {
            throw InvalidConfigException::becauseValueIsInvalid(self::CONFIG_KEY, 'an array', $result);
        }

        return $result;
    }

    private static function getConfigRepository(Container $app): Repository
    {
        $result = $app->make('config');

        \assert($result instanceof Repository);

        return $result;
    }
}
