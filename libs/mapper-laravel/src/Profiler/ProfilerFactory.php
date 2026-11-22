<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel\Profiler;

use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DebugBar;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Laravel\Telescope\Contracts\EntriesRepository;
use Laravel\Telescope\Telescope;
use TypeLang\Mapper\Bridge\Laravel\Debugbar\MapperCollector;
use TypeLang\Mapper\Bridge\Laravel\Exception\InvalidConfigException;
use TypeLang\Mapper\Bridge\Laravel\Telescope\MapperRecorder;
use TypeLang\Mapper\Profiler\ProfilerInterface;

/**
 * Creates the profiler of the mappers from the Debugbar and Telescope
 * enabled in the `type-lang-mapper.profiler` config, by default each one
 * together with its tool.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Bridge\Laravel
 */
final class ProfilerFactory
{
    /**
     * Prefix of the settings in the messages of the errors
     */
    private const CONFIG_KEY = 'type-lang-mapper.profiler';

    public function __construct(
        private readonly Container $container,
        private readonly Repository $repository,
    ) {}

    /**
     * Returns {@see null} in case of no profiler is enabled.
     *
     * @param array<array-key, mixed> $config the `type-lang-mapper` config
     * @throws InvalidConfigException in case of the profiler config is
     *         invalid or a profiler is enabled without its tool
     */
    public function create(array $config): ?ProfilerInterface
    {
        $config = $config['profiler'] ?? [];

        if (!\is_array($config)) {
            throw InvalidConfigException::becauseValueIsInvalid(self::CONFIG_KEY, 'an array', $config);
        }

        $profilers = [];

        $debugbar = $this->createDebugbarProfiler(self::readFlag($config, 'debugbar'));

        if ($debugbar !== null) {
            $profilers[] = $debugbar;
        }

        $telescope = $this->createTelescopeProfiler(self::readFlag($config, 'telescope'));

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
     * Returns the collector registered in the Debugbar, adds it in case of
     * it is missing.
     *
     * @param bool|null $isEnabled the flag of the config, {@see null} enables
     *        the profiler together with the Debugbar
     * @throws InvalidConfigException in case of the profiler is enabled
     *         without the Debugbar
     */
    private function createDebugbarProfiler(?bool $isEnabled): ?MapperCollector
    {
        if ($isEnabled === false) {
            return null;
        }

        $debugbar = null;

        if ($this->container->bound('debugbar')) {
            $debugbar = $this->container->make('debugbar');
        }

        if (!$debugbar instanceof DebugBar) {
            if ($isEnabled === true) {
                throw InvalidConfigException::becauseProfilerRequiresPackage(
                    key: self::CONFIG_KEY . '.debugbar',
                    package: 'barryvdh/laravel-debugbar',
                );
            }

            return null;
        }

        if ($isEnabled === null && \method_exists($debugbar, 'isEnabled') && $debugbar->isEnabled() !== true) {
            return null;
        }

        if (!$debugbar->hasCollector(MapperCollector::NAME)) {
            $debugbar->addCollector(new MapperCollector(self::findTimeline($debugbar)));
        }

        $result = $debugbar->getCollector(MapperCollector::NAME);

        \assert($result instanceof MapperCollector);

        return $result;
    }

    private static function findTimeline(DebugBar $debugbar): ?TimeDataCollector
    {
        if (!$debugbar->hasCollector('time')) {
            return null;
        }

        $result = $debugbar->getCollector('time');

        if ($result instanceof TimeDataCollector) {
            return $result;
        }

        return null;
    }

    /**
     * @param bool|null $isEnabled the flag of the config, {@see null} enables
     *        the profiler together with Telescope
     * @throws InvalidConfigException in case of the profiler is enabled
     *         without Telescope
     */
    private function createTelescopeProfiler(?bool $isEnabled): ?MapperRecorder
    {
        if ($isEnabled === false) {
            return null;
        }

        if (!\class_exists(Telescope::class) || !$this->container->bound(EntriesRepository::class)) {
            if ($isEnabled === true) {
                throw InvalidConfigException::becauseProfilerRequiresPackage(
                    key: self::CONFIG_KEY . '.telescope',
                    package: 'laravel/telescope',
                );
            }

            return null;
        }

        if ($isEnabled === null && !$this->isTelescopeEnabled()) {
            return null;
        }

        return new MapperRecorder();
    }

    private function isTelescopeEnabled(): bool
    {
        return \filter_var($this->repository->get('telescope.enabled'), \FILTER_VALIDATE_BOOL);
    }

    /**
     * @param array<array-key, mixed> $config the `type-lang-mapper.profiler`
     *        config
     * @param non-empty-string $name
     * @throws InvalidConfigException in case of the flag is not a boolean
     */
    private static function readFlag(array $config, string $name): ?bool
    {
        $result = $config[$name] ?? null;

        if ($result !== null && !\is_bool($result)) {
            throw InvalidConfigException::becauseValueIsInvalid(
                key: self::CONFIG_KEY . '.' . $name,
                expected: 'a boolean',
                value: $result,
            );
        }

        return $result;
    }
}
