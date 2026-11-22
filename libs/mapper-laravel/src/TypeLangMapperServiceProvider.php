<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use TypeLang\Mapper\Bridge\Laravel\Exception\InvalidConfigException;
use TypeLang\Mapper\Bridge\Laravel\Profiler\ProfilerFactory;
use TypeLang\Mapper\DenormalizerInterface;
use TypeLang\Mapper\MapperInterface;
use TypeLang\Mapper\NormalizerInterface;

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

        $this->app->singleton(MapperFactory::class, self::createFactory(...));

        $this->registerMapper(MapperFactory::DEFAULT_NAME);

        foreach (self::MAPPER_INTERFACES as $interface) {
            $this->app->alias(self::MAPPER_ID, $interface);
        }

        $config = self::readConfig(self::getConfigRepository($this->app));

        foreach (MapperFactory::getMapperNames($config) as $name) {
            $this->registerMapper($name);
        }
    }

    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            self::CONFIG_PATH => $this->app->configPath(self::CONFIG_KEY . '.php'),
        ], self::CONFIG_KEY . '-config');
    }

    /**
     * @param non-empty-string $name
     */
    private function registerMapper(string $name): void
    {
        $this->app->singleton(
            self::getMapperId($name),
            static fn(Container $app): MapperInterface => $app->make(MapperFactory::class)->create($name),
        );
    }

    /**
     * @throws InvalidConfigException in case of the config is invalid
     */
    private static function createFactory(Container $app): MapperFactory
    {
        $repository = self::getConfigRepository($app);
        $config = self::readConfig($repository);

        return new MapperFactory(
            container: $app,
            config: $config,
            isDebug: $repository->get('app.debug') === true,
            profiler: (new ProfilerFactory($app, $repository))->create($config),
        );
    }

    /**
     * @return array<array-key, mixed>
     * @throws InvalidConfigException in case of the config is not an array
     */
    private static function readConfig(Repository $repository): array
    {
        $result = $repository->get(self::CONFIG_KEY, []);

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
