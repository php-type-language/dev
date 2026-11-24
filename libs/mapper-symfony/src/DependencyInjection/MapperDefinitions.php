<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use TypeLang\Mapper\Bridge\Symfony\Profiler\MapperDataCollector;
use TypeLang\Mapper\Configuration;
use TypeLang\Mapper\DenormalizerInterface;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\MapperInterface;
use TypeLang\Mapper\Mapping\MetadataBuilder;
use TypeLang\Mapper\Mapping\Provider\CacheMetadataProvider;
use TypeLang\Mapper\Mapping\Provider\MetadataProviderInterface;
use TypeLang\Mapper\Mapping\Reader\FileMetadataReader;
use TypeLang\Mapper\Mapping\Reader\JsonFileMetadataReader;
use TypeLang\Mapper\Mapping\Reader\NeonFileMetadataReader;
use TypeLang\Mapper\Mapping\Reader\PhpFileMetadataReader;
use TypeLang\Mapper\Mapping\Reader\YamlFileMetadataReader;
use TypeLang\Mapper\Naming\CamelCaseNamingStrategy;
use TypeLang\Mapper\Naming\KebabCaseNamingStrategy;
use TypeLang\Mapper\Naming\PascalCaseNamingStrategy;
use TypeLang\Mapper\Naming\ScreamingSnakeCaseNamingStrategy;
use TypeLang\Mapper\Naming\SnakeCaseNamingStrategy;
use TypeLang\Mapper\NormalizerInterface;
use TypeLang\Mapper\Profiler\TraceableMapper;
use TypeLang\Mapper\Profiler\TraceableMetadataProvider;
use TypeLang\Mapper\Profiler\TypeBuildRecorder;
use TypeLang\Parser\TypeParser;

/**
 * Registers the services of the mappers from the processed config. The
 * extensions of a mapper (builders, hooks, strategies, readers) are filled
 * in by {@see MapperExtensionsPass}.
 *
 * @phpstan-type MapperSettingsType array{
 *     strict: bool|null,
 *     normalize_as_array: bool|null,
 *     max_violations: int<1, max>|null,
 *     reject_extra_fields: bool|null,
 *     expected_type_depth: int<0, max>|null,
 *     expected_type_max_fields: int<1, max>|null,
 *     max_depth: int<1, max>|null,
 *     naming: non-empty-string|null,
 *     expression_language: non-empty-string|null,
 *     types: list<non-empty-string>,
 *     decorators: list<non-empty-string>,
 *     expanders: list<non-empty-string>,
 *     hooks: list<non-empty-string>,
 *     instantiators: list<non-empty-string>,
 *     accessors: list<non-empty-string>,
 *     coercers: array<non-empty-string, non-empty-string|null>,
 *     formatters: array<non-empty-string, non-empty-string|null>,
 *     metadata: array{
 *         readers: list<non-empty-string>,
 *         paths: list<non-empty-string>,
 *         selector: non-empty-string|null,
 *         cache: array{
 *             enabled: bool|null,
 *             pools: list<array{
 *                 psr6: non-empty-string|null,
 *                 psr16: non-empty-string|null,
 *                 prefix: string|null,
 *             }>,
 *         },
 *     },
 *     ...
 * }
 * @phpstan-type BundleConfigType array{
 *     strict: bool|null,
 *     normalize_as_array: bool|null,
 *     max_violations: int<1, max>|null,
 *     reject_extra_fields: bool|null,
 *     expected_type_depth: int<0, max>|null,
 *     expected_type_max_fields: int<1, max>|null,
 *     max_depth: int<1, max>|null,
 *     naming: non-empty-string|null,
 *     expression_language: non-empty-string|null,
 *     types: list<non-empty-string>,
 *     decorators: list<non-empty-string>,
 *     expanders: list<non-empty-string>,
 *     hooks: list<non-empty-string>,
 *     instantiators: list<non-empty-string>,
 *     accessors: list<non-empty-string>,
 *     coercers: array<non-empty-string, non-empty-string|null>,
 *     formatters: array<non-empty-string, non-empty-string|null>,
 *     metadata: array{
 *         readers: list<non-empty-string>,
 *         paths: list<non-empty-string>,
 *         selector: non-empty-string|null,
 *         cache: array{
 *             enabled: bool|null,
 *             pools: list<array{
 *                 psr6: non-empty-string|null,
 *                 psr16: non-empty-string|null,
 *                 prefix: string|null,
 *             }>,
 *         },
 *     },
 *     mappers: array<non-empty-string, MapperSettingsType>,
 *     profiler: array{
 *         enabled: bool|null,
 *     },
 * }
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Bridge\Symfony
 */
final class MapperDefinitions
{
    /**
     * Name of the default mapper, inherited by the named ones
     */
    public const DEFAULT_NAME = 'default';

    public const MAPPER_ID = 'type_lang_mapper.mapper';

    public const PARSER_ID = 'type_lang_mapper.parser';

    /**
     * Language of the skip expressions of the mappers without their own
     * one, registered in case of `symfony/expression-language` is installed
     */
    public const EXPRESSION_LANGUAGE_ID = 'type_lang_mapper.expression_language';

    /**
     * Tag of the {@see ExpressionFunctionProviderInterface} services giving
     * their functions to {@see EXPRESSION_LANGUAGE_ID}
     */
    public const EXPRESSION_LANGUAGE_PROVIDER_TAG = 'type_lang_mapper.expression_language_provider';

    public const COLLECTOR_ID = 'type_lang_mapper.data_collector';

    /**
     * Contracts of a mapper service, the aliases of the default mapper
     */
    private const MAPPER_INTERFACES = [
        MapperInterface::class,
        DenormalizerInterface::class,
        NormalizerInterface::class,
    ];

    /**
     * Pool of the metadata cache used in case of the config sets none,
     * declared with FrameworkBundle
     */
    public const DEFAULT_CACHE_POOL = 'cache.type_lang_mapper';

    /**
     * Parameter passing the configured extensions to
     * {@see MapperExtensionsPass}, removed by the pass
     */
    public const EXTENSIONS_PARAMETER = '.type_lang_mapper.extensions';

    /**
     * Parameter passing the configured services of the {@see MapperSlot}
     * roles to {@see MapperExtensionsPass}, removed by the pass
     */
    public const SLOTS_PARAMETER = '.type_lang_mapper.slots';

    /**
     * Built-in naming strategies by the name of the style written in that
     * style, as in the {@see \TypeLang\Mapper\Mapping\NamingStrategy}
     * attribute
     */
    private const NAMING_STRATEGIES = [
        'camelCase' => CamelCaseNamingStrategy::class,
        'PascalCase' => PascalCaseNamingStrategy::class,
        'snake_case' => SnakeCaseNamingStrategy::class,
        'SCREAMING_SNAKE_CASE' => ScreamingSnakeCaseNamingStrategy::class,
        'kebab-case' => KebabCaseNamingStrategy::class,
    ];

    /**
     * Readers of the mapping config files by the file extension
     */
    private const FILE_READERS = [
        'yaml' => YamlFileMetadataReader::class,
        'yml' => YamlFileMetadataReader::class,
        'neon' => NeonFileMetadataReader::class,
        'json' => JsonFileMetadataReader::class,
        'php' => PhpFileMetadataReader::class,
    ];

    private const FILE_PATTERN = '/\.(?:ya?ml|neon|json|php)$/i';

    public function __construct(
        private readonly ContainerBuilder $container,
    ) {}

    /**
     * Returns the id of the mapper service.
     *
     * @param non-empty-string $name
     * @return non-empty-string
     */
    public static function getMapperId(string $name): string
    {
        if ($name === self::DEFAULT_NAME) {
            return self::MAPPER_ID;
        }

        return self::MAPPER_ID . '.' . $name;
    }

    /**
     * Returns the id of the service of the mapper taking the extensions of
     * the kind.
     *
     * @param non-empty-string $name
     * @return non-empty-string
     */
    public static function getExtensionTargetId(string $name, ExtensionKind $kind): string
    {
        return match ($kind) {
            ExtensionKind::TypeBuilder,
            ExtensionKind::TypeDecorator,
            ExtensionKind::TypeExpander,
            ExtensionKind::ClassHook,
            ExtensionKind::InstantiationStrategy,
            ExtensionKind::AccessStrategy => self::getMapperId($name),
            ExtensionKind::MetadataReader => self::getMetadataBuilderId($name),
        };
    }

    /**
     * @param array<array-key, mixed> $config processed config of the bundle
     * @throws InvalidConfigurationException in case of a mapping config path
     *         does not exist or its format is not supported
     */
    public function register(array $config): void
    {
        /** @var BundleConfigType $config */
        $this->container->register(self::PARSER_ID, TypeParser::class);

        if (\class_exists(ExpressionLanguage::class)) {
            $this->container->register(self::EXPRESSION_LANGUAGE_ID, ExpressionLanguage::class)
                ->setArguments([null, new TaggedIteratorArgument(self::EXPRESSION_LANGUAGE_PROVIDER_TAG)]);
        }

        $defaults = $config;
        $extensions = [];
        $slots = [];

        $extensions[self::DEFAULT_NAME] = $this->registerMapper(self::DEFAULT_NAME, $defaults);
        $slots[self::DEFAULT_NAME] = self::getConfiguredSlots($defaults);

        // The interfaces are autowired to the default mapper only, whatever
        // the name of the argument. A named mapper is injected by its id.
        foreach (self::MAPPER_INTERFACES as $type) {
            $this->container->setAlias($type, self::MAPPER_ID);
        }

        foreach ($config['mappers'] as $name => $settings) {
            $extensions[$name] = $this->registerMapper($name, $settings, $defaults);
            $slots[$name] = self::getConfiguredSlots($settings);
        }

        $this->container->setParameter(self::EXTENSIONS_PARAMETER, $extensions);
        $this->container->setParameter(self::SLOTS_PARAMETER, $slots);

        if ($config['profiler']['enabled'] ?? $this->isDebug()) {
            $this->registerProfilerServices(\array_keys($extensions));
        }
    }

    /**
     * Returns the id of the type decorator reporting the built types of the
     * mapper to the profiler.
     *
     * @param non-empty-string $name
     * @return non-empty-string
     */
    public static function getTypeRecorderId(string $name): string
    {
        return self::getInternalId($name, 'type_recorder');
    }

    /**
     * Decorates the mappers and their metadata providers to report to the
     * collector of the web profiler.
     *
     * @param list<non-empty-string> $mappers
     */
    private function registerProfilerServices(array $mappers): void
    {
        $this->container->register(self::COLLECTOR_ID, MapperDataCollector::class)
            ->setArguments([
                '$stopwatch' => new Reference('debug.stopwatch', ContainerInterface::NULL_ON_INVALID_REFERENCE),
            ])
            ->addTag('data_collector', [
                'id' => 'type_lang_mapper',
                'template' => '@TypeLangMapper/Collector/mapper.html.twig',
            ]);

        $collector = new Reference(self::COLLECTOR_ID);

        foreach ($mappers as $name) {
            $this->container->register(self::getInternalId($name, 'traceable'), TraceableMapper::class)
                ->setDecoratedService(self::getMapperId($name))
                ->setArguments([
                    '$delegate' => new Reference('.inner'),
                    '$name' => $name,
                    '$profiler' => $collector,
                ]);

            $this->container->register(self::getInternalId($name, 'traceable_metadata'), TraceableMetadataProvider::class)
                ->setDecoratedService(self::getInternalId($name, 'metadata'))
                ->setArguments([
                    '$delegate' => new Reference('.inner'),
                    '$name' => $name,
                    '$profiler' => $collector,
                ]);

            $this->container->register(self::getTypeRecorderId($name), TypeBuildRecorder::class)
                ->setArguments(['$name' => $name, '$profiler' => $collector]);
        }
    }

    /**
     * Returns the services of the {@see MapperSlot} roles given in the
     * settings of a mapper by the key, without the inherited ones.
     *
     * @param MapperSettingsType $settings
     * @return array<value-of<MapperSlot>, array<non-empty-string, non-empty-string>>
     */
    private static function getConfiguredSlots(array $settings): array
    {
        $result = [];

        foreach (MapperSlot::cases() as $slot) {
            $result[$slot->value] = $slot->getConfiguredServices($settings);
        }

        return $result;
    }

    /**
     * Registers the mapper, returns the services of its config by the kind
     * of the extension.
     *
     * @param non-empty-string $name
     * @param MapperSettingsType $settings
     * @param MapperSettingsType|null $defaults settings of the default
     *        mapper inherited by the named one
     * @return array<value-of<ExtensionKind>, list<non-empty-string>>
     * @throws InvalidConfigurationException in case of a mapping config path
     *         does not exist or its format is not supported
     */
    private function registerMapper(string $name, array $settings, ?array $defaults = null): array
    {
        $this->container->register(self::getInternalId($name, 'configuration'), Configuration::class)
            ->setArguments(self::filterNulls([
                '$strict' => $settings['strict'] ?? $defaults['strict'] ?? null,
                '$normalizeAsArray' => $settings['normalize_as_array'] ?? $defaults['normalize_as_array'] ?? null,
                '$maxViolations' => $settings['max_violations'] ?? $defaults['max_violations'] ?? null,
                '$rejectExtraFields' => $settings['reject_extra_fields']
                    ?? $defaults['reject_extra_fields']
                    ?? null,
                '$expectedTypeDepth' => $settings['expected_type_depth'] ?? $defaults['expected_type_depth'] ?? null,
                '$expectedTypeMaxFields' => $settings['expected_type_max_fields']
                    ?? $defaults['expected_type_max_fields']
                    ?? null,
                '$maxDepth' => $settings['max_depth'] ?? $defaults['max_depth'] ?? null,
            ]));

        $expressions = $this->findExpressionLanguage($settings, $defaults);

        $this->registerMetadata($name, $settings, $defaults, $expressions);

        $this->container->register(self::getMapperId($name), Mapper::class)
            ->setArguments([
                '$config' => new Reference(self::getInternalId($name, 'configuration')),
                '$metadata' => new Reference(self::getInternalId($name, 'metadata')),
                '$builders' => [],
                '$decorators' => [],
                '$expanders' => [],
                '$parser' => new Reference(self::PARSER_ID),
                '$expressions' => $expressions,
                '$accessors' => [],
                '$instantiators' => [],
                '$hooks' => [],
                '$coercers' => [],
            ]);

        $extensions = [];

        foreach (ExtensionKind::cases() as $kind) {
            $extensions[$kind->value] = $kind->getConfiguredServices($settings);
        }

        $extensions[ExtensionKind::MetadataReader->value] = [
            ...$extensions[ExtensionKind::MetadataReader->value],
            ...$this->registerFileReaders($settings['metadata']['paths'], $expressions),
        ];

        return $extensions;
    }

    /**
     * Returns the id of the builder of the metadata provider of the mapper.
     *
     * @param non-empty-string $name
     * @return non-empty-string
     */
    public static function getMetadataBuilderId(string $name): string
    {
        return self::getInternalId($name, 'metadata_builder');
    }

    /**
     * Returns the reference to the language of the skip expressions of the
     * `expression_language` setting or of the bundle, {@see null} in case of
     * neither is available.
     *
     * @param MapperSettingsType $settings
     * @param MapperSettingsType|null $defaults
     */
    private function findExpressionLanguage(array $settings, ?array $defaults): ?Reference
    {
        $id = $settings['expression_language'] ?? $defaults['expression_language'] ?? null;

        if ($id !== null) {
            return new Reference($id);
        }

        if ($this->container->hasDefinition(self::EXPRESSION_LANGUAGE_ID)) {
            return new Reference(self::EXPRESSION_LANGUAGE_ID);
        }

        return null;
    }

    /**
     * Registers the builder of the metadata provider of the mapper and the
     * provider built by it. The readers are added by
     * {@see MapperExtensionsPass}.
     *
     * @param non-empty-string $name
     * @param MapperSettingsType $settings
     * @param MapperSettingsType|null $defaults
     * @param Reference|null $expressions language of the skip expressions
     */
    private function registerMetadata(
        string $name,
        array $settings,
        ?array $defaults,
        ?Reference $expressions,
    ): void {
        $builder = $this->container->register(self::getMetadataBuilderId($name), MetadataBuilder::class)
            ->setFactory([MetadataBuilder::class, 'createDefault'])
            ->setArguments([
                '$parser' => new Reference(self::PARSER_ID),
                '$expressions' => $expressions,
            ]);

        $strategy = $this->createNamingStrategy($settings['naming'] ?? $defaults['naming'] ?? null);

        if ($strategy !== null) {
            $builder->addMethodCall('withNamingStrategy', [$strategy], true);
        }

        $selector = $settings['metadata']['selector'] ?? $defaults['metadata']['selector'] ?? null;

        if ($selector !== null) {
            $builder->addMethodCall('withReflectionReader', [new Reference($selector)], true);
        }

        foreach ($this->createCacheCalls($name, $settings, $defaults) as [$method, $arguments]) {
            $builder->addMethodCall($method, $arguments, true);
        }

        $this->container->register(self::getInternalId($name, 'metadata'), MetadataProviderInterface::class)
            ->setFactory([new Reference(self::getMetadataBuilderId($name)), 'build']);
    }

    /**
     * Registers a reader per mapping config file, returns their ids in the
     * order of the paths, the files of a directory by the name. A reader is
     * shared by the mappers with the same language of the skip expressions.
     *
     * @param list<non-empty-string> $paths
     * @param Reference|null $expressions language of the skip expressions
     * @return list<non-empty-string>
     * @throws InvalidConfigurationException in case of a path does not exist
     *         or its format is not supported
     */
    private function registerFileReaders(array $paths, ?Reference $expressions): array
    {
        $result = [];

        foreach ($paths as $path) {
            foreach ($this->findConfigFiles($path) as $file) {
                $id = '.type_lang_mapper.file_reader.' . \hash('xxh128', $file . "\0" . $expressions);

                if (!$this->container->hasDefinition($id)) {
                    $this->container->register($id, self::getFileReaderClass($file))
                        ->setArguments([$file, new Reference(self::PARSER_ID), $expressions]);
                }

                $result[] = $id;
            }
        }

        return $result;
    }

    /**
     * @param non-empty-string $path
     * @return list<non-empty-string>
     * @throws InvalidConfigurationException in case of the path does not
     *         exist
     */
    private function findConfigFiles(string $path): array
    {
        /** @var non-empty-string $path */
        $path = $this->container->getParameterBag()->resolveValue($path);

        if (!$this->container->fileExists($path, self::FILE_PATTERN)) {
            throw new InvalidConfigurationException(\sprintf(
                'Mapping config path "%s" does not exist',
                \addcslashes($path, '"'),
            ));
        }

        if (!\is_dir($path)) {
            return [$path];
        }

        $files = \scandir($path);
        $result = [];

        if ($files === false) {
            return [];
        }

        foreach ($files as $file) {
            $file = $path . \DIRECTORY_SEPARATOR . $file;

            if (\is_file($file) && \preg_match(self::FILE_PATTERN, $file) === 1) {
                $result[] = $file;
            }
        }

        return $result;
    }

    /**
     * @param non-empty-string $file
     * @return class-string<FileMetadataReader>
     * @throws InvalidConfigurationException in case of the format of the
     *         file is not supported or its package is not installed
     */
    private static function getFileReaderClass(string $file): string
    {
        $extension = \strtolower(\pathinfo($file, \PATHINFO_EXTENSION));
        $class = self::FILE_READERS[$extension] ?? null;

        if ($class === null) {
            throw new InvalidConfigurationException(\sprintf(
                'Mapping config file "%s" is not supported, supported extensions are %s',
                \addcslashes($file, '"'),
                \implode(', ', \array_keys(self::FILE_READERS)),
            ));
        }

        if (\is_a($class, YamlFileMetadataReader::class, true) && !YamlFileMetadataReader::isAvailable()) {
            throw self::becausePackageIsMissing($file, 'symfony/yaml');
        }

        if (\is_a($class, NeonFileMetadataReader::class, true) && !NeonFileMetadataReader::isAvailable()) {
            throw self::becausePackageIsMissing($file, 'nette/neon');
        }

        if (\is_a($class, JsonFileMetadataReader::class, true) && !JsonFileMetadataReader::isAvailable()) {
            throw self::becausePackageIsMissing($file, 'ext-json');
        }

        return $class;
    }

    /**
     * @param non-empty-string $file
     * @param non-empty-string $package
     */
    private static function becausePackageIsMissing(string $file, string $package): InvalidConfigurationException
    {
        return new InvalidConfigurationException(\sprintf(
            'Mapping config file "%s" requires the "%s" package',
            \addcslashes($file, '"'),
            $package,
        ));
    }

    /**
     * @param non-empty-string|null $name
     */
    private function createNamingStrategy(?string $name): Definition|Reference|null
    {
        if ($name === null) {
            return null;
        }

        $class = self::NAMING_STRATEGIES[$name] ?? null;

        if ($class !== null) {
            return new Definition($class);
        }

        return new Reference($name);
    }

    /**
     * Returns the calls of the builder adding the caches of the metadata,
     * none in case of the cache is disabled. The pools of a named mapper
     * replace the ones of the default mapper. Without pools the default
     * pool is used, it is skipped in case of it is not declared and the
     * cache is not enabled in the config.
     *
     * @param non-empty-string $name
     * @param MapperSettingsType $settings
     * @param MapperSettingsType|null $defaults
     * @return list<array{non-empty-string, list<mixed>}>
     */
    private function createCacheCalls(string $name, array $settings, ?array $defaults): array
    {
        $isEnabled = $settings['metadata']['cache']['enabled']
            ?? $defaults['metadata']['cache']['enabled']
            ?? null;

        if ($isEnabled === false || ($isEnabled === null && $this->isDebug())) {
            return [];
        }

        $prefix = CacheMetadataProvider::DEFAULT_PREFIX . $name . '_';
        $pools = $settings['metadata']['cache']['pools'];

        if ($pools === [] && $defaults !== null) {
            $pools = $defaults['metadata']['cache']['pools'];
        }

        if ($pools === [] && $isEnabled === null) {
            $pool = new Reference(self::DEFAULT_CACHE_POOL, ContainerInterface::IGNORE_ON_INVALID_REFERENCE);

            return [['withPsr6Cache', [$pool, $prefix]]];
        }

        if ($pools === []) {
            return [['withPsr6Cache', [new Reference(self::DEFAULT_CACHE_POOL), $prefix]]];
        }

        $result = [];

        foreach ($pools as $pool) {
            if ($pool['psr6'] !== null) {
                $result[] = ['withPsr6Cache', [new Reference($pool['psr6']), $pool['prefix'] ?? $prefix]];
            }

            if ($pool['psr16'] !== null) {
                $result[] = ['withPsr16Cache', [new Reference($pool['psr16']), $pool['prefix'] ?? $prefix]];
            }
        }

        return $result;
    }

    private function isDebug(): bool
    {
        return $this->container->hasParameter('kernel.debug')
            && $this->container->getParameter('kernel.debug') === true;
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $service
     * @return non-empty-string
     */
    private static function getInternalId(string $name, string $service): string
    {
        return '.type_lang_mapper.' . $name . '.' . $service;
    }

    /**
     * @param array<non-empty-string, mixed> $arguments
     * @return array<non-empty-string, mixed>
     */
    private static function filterNulls(array $arguments): array
    {
        return \array_filter($arguments, static fn(mixed $value): bool => $value !== null);
    }
}
