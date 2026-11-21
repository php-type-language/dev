<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel;

use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DebugBar;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use TypeLang\Mapper\Bridge\Laravel\Debugbar\MapperCollector;
use TypeLang\Mapper\Bridge\Laravel\Exception\InvalidConfigException;
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
     * Returns the collector of the Debugbar in case of the profiler is
     * enabled, by default together with the Debugbar.
     *
     * @throws InvalidConfigException in case of the profiler is enabled
     *         without the Debugbar
     */
    private static function findProfiler(Container $app): ?ProfilerInterface
    {
        $isEnabled = self::readConfig($app)['profiler'] ?? null;

        if ($isEnabled !== null && !\is_bool($isEnabled)) {
            throw InvalidConfigException::becauseValueIsInvalid(self::CONFIG_KEY . '.profiler', 'a boolean', $isEnabled);
        }

        if ($isEnabled === false) {
            return null;
        }

        $debugbar = $app->bound('debugbar') ? $app->make('debugbar') : null;

        if (!$debugbar instanceof DebugBar) {
            if ($isEnabled === true) {
                throw InvalidConfigException::becauseProfilerRequiresDebugbar();
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
