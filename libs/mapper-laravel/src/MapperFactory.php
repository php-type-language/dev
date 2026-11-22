<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Container\Container;
use TypeLang\Mapper\Bridge\Laravel\Exception\InvalidConfigException;
use TypeLang\Mapper\Coercer\CoercerInterface;
use TypeLang\Mapper\Configuration;
use TypeLang\Mapper\Diagnostics\ViolationFormatterInterface;
use TypeLang\Mapper\Hook\ClassHookInterface;
use TypeLang\Mapper\Instantiator\Strategy\InstantiationStrategyInterface;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\MapperInterface;
use TypeLang\Mapper\Mapping\MetadataBuilder;
use TypeLang\Mapper\Mapping\Provider\CacheMetadataProvider;
use TypeLang\Mapper\Mapping\Provider\MetadataProviderInterface;
use TypeLang\Mapper\Mapping\Reader\FileMetadataReader;
use TypeLang\Mapper\Mapping\Reader\JsonFileMetadataReader;
use TypeLang\Mapper\Mapping\Reader\MetadataReaderInterface;
use TypeLang\Mapper\Mapping\Reader\NeonFileMetadataReader;
use TypeLang\Mapper\Mapping\Reader\PhpFileMetadataReader;
use TypeLang\Mapper\Mapping\Reader\Reflection\PropertySelectorInterface;
use TypeLang\Mapper\Mapping\Reader\YamlFileMetadataReader;
use TypeLang\Mapper\Naming\CamelCaseNamingStrategy;
use TypeLang\Mapper\Naming\KebabCaseNamingStrategy;
use TypeLang\Mapper\Naming\NamingStrategyInterface;
use TypeLang\Mapper\Naming\PascalCaseNamingStrategy;
use TypeLang\Mapper\Naming\ScreamingSnakeCaseNamingStrategy;
use TypeLang\Mapper\Naming\SnakeCaseNamingStrategy;
use TypeLang\Mapper\Profiler\ProfilerInterface;
use TypeLang\Mapper\Profiler\TraceableMapper;
use TypeLang\Mapper\Profiler\TraceableMetadataProvider;
use TypeLang\Mapper\Profiler\TypeBuildRecorder;
use TypeLang\Mapper\PropertyAccessor\Strategy\AccessStrategyInterface;
use TypeLang\Mapper\Type\Builder\TypeBuilderInterface;
use TypeLang\Mapper\Type\Decorator\TypeDecoratorInterface;
use TypeLang\Mapper\Type\Expander\TypeExpanderInterface;
use TypeLang\Parser\TypeParser;

/**
 * Creates the mappers from the `type-lang-mapper` config, the services of
 * the extensions are taken from the container.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Bridge\Laravel
 */
final class MapperFactory
{
    /**
     * Name of the default mapper, inherited by the named ones
     */
    public const DEFAULT_NAME = 'default';

    /**
     * Pattern of the name of a named mapper
     */
    private const MAPPER_NAME_PATTERN = '/^[a-z][a-z0-9_]*$/';

    /**
     * Prefix of the settings in the messages of the errors
     */
    private const CONFIG_KEY = 'type-lang-mapper';

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

    private readonly TypeParser $parser;

    public function __construct(
        private readonly Container $container,
        /**
         * The `type-lang-mapper` config
         *
         * @var array<array-key, mixed>
         */
        private readonly array $config,
        private readonly bool $isDebug = false,
        /**
         * Profiler the mappers report to, the mappers are not traced in
         * case of {@see null}
         */
        private readonly ?ProfilerInterface $profiler = null,
    ) {
        $this->parser = new TypeParser();
    }

    /**
     * Returns the names of the named mappers of the config.
     *
     * @param array<array-key, mixed> $config the `type-lang-mapper` config
     * @return list<non-empty-string>
     * @throws InvalidConfigException in case of a name is invalid
     */
    public static function getMapperNames(array $config): array
    {
        $mappers = $config['mappers'] ?? [];

        if (!\is_array($mappers)) {
            throw InvalidConfigException::becauseValueIsInvalid(self::CONFIG_KEY . '.mappers', 'an array', $mappers);
        }

        $result = [];

        foreach ($mappers as $name => $_) {
            $name = (string) $name;

            if ($name === self::DEFAULT_NAME || \preg_match(self::MAPPER_NAME_PATTERN, $name) !== 1) {
                throw InvalidConfigException::becauseMapperNameIsInvalid(
                    $name,
                    self::MAPPER_NAME_PATTERN,
                    self::DEFAULT_NAME,
                );
            }

            $result[] = $name;
        }

        return $result;
    }

    /**
     * @param non-empty-string $name
     * @throws InvalidConfigException in case of the config of the mapper is
     *         invalid
     */
    public function create(string $name): MapperInterface
    {
        $settings = $this->getSettings($name);
        $decorators = $this->makeServices($settings->getList('decorators'), TypeDecoratorInterface::class);
        $metadata = $this->createMetadataProvider($name, $settings);
        $formatters = $this->makeFormatters($settings);

        if ($this->profiler !== null) {
            $decorators[] = new TypeBuildRecorder($name, $this->profiler);
            $metadata = new TraceableMetadataProvider($metadata, $name, $this->profiler);
        }

        $mapper = new Mapper(
            config: $this->createConfiguration($settings),
            builders: $this->makeServices($settings->getList('types'), TypeBuilderInterface::class),
            hooks: $this->makeServices($settings->getList('hooks'), ClassHookInterface::class),
            decorators: $decorators,
            expanders: $this->makeServices($settings->getList('expanders'), TypeExpanderInterface::class),
            accessors: $this->makeServices($settings->getList('accessors'), AccessStrategyInterface::class),
            instantiators: $this->makeServices(
                $settings->getList('instantiators'),
                InstantiationStrategyInterface::class,
            ),
            coercers: $this->makeCoercers($settings),
            metadata: $metadata,
            denormalizationFormatter: $formatters['denormalization'] ?? null,
            normalizationFormatter: $formatters['normalization'] ?? null,
            parser: $this->parser,
        );

        if ($this->profiler === null) {
            return $mapper;
        }

        return new TraceableMapper($mapper, $name, $this->profiler);
    }

    /**
     * @param non-empty-string $name
     * @throws InvalidConfigException in case of the mapper is not configured
     */
    private function getSettings(string $name): MapperSettings
    {
        if ($name === self::DEFAULT_NAME) {
            return new MapperSettings(self::CONFIG_KEY, $this->config);
        }

        $mappers = $this->config['mappers'] ?? null;
        $settings = \is_array($mappers) ? $mappers[$name] ?? null : null;
        $key = self::CONFIG_KEY . '.mappers.' . $name;

        if (!\is_array($settings)) {
            throw InvalidConfigException::becauseValueIsInvalid($key, 'an array', $settings);
        }

        return new MapperSettings($key, $settings, $this->config);
    }

    /**
     * @throws InvalidConfigException in case of a setting is invalid
     */
    private function createConfiguration(MapperSettings $settings): Configuration
    {
        $result = new Configuration();

        $strict = $settings->findBool('strict');

        if ($strict !== null) {
            $result = $result->withStrict($strict);
        }

        $normalizeAsArray = $settings->findBool('normalize_as_array');

        if ($normalizeAsArray !== null) {
            $result = $result->withNormalizeAsArray($normalizeAsArray);
        }

        $maxViolations = $settings->findPositiveInt('max_violations');

        if ($maxViolations !== null) {
            $result = $result->withMaxViolations($maxViolations);
        }

        $rejectExtraFields = $settings->findBool('reject_extra_fields');

        if ($rejectExtraFields !== null) {
            $result = $result->withRejectExtraFields($rejectExtraFields);
        }

        $expectedTypeDepth = $settings->findNonNegativeInt('expected_type_depth');

        if ($expectedTypeDepth !== null) {
            $result = $result->withExpectedTypeDepth($expectedTypeDepth);
        }

        $expectedTypeMaxFields = $settings->findPositiveInt('expected_type_max_fields');

        if ($expectedTypeMaxFields !== null) {
            $result = $result->withExpectedTypeMaxFields($expectedTypeMaxFields);
        }

        return $result;
    }

    /**
     * @param non-empty-string $name
     * @throws InvalidConfigException in case of a setting is invalid
     */
    private function createMetadataProvider(string $name, MapperSettings $settings): MetadataProviderInterface
    {
        $builder = MetadataBuilder::createDefault($this->parser);

        $naming = $settings->findString('naming');

        if ($naming !== null) {
            $builder = $builder->withNamingStrategy($this->createNamingStrategy($naming));
        }

        $selector = $settings->findString('metadata.selector');

        if ($selector !== null) {
            $builder = $builder->withReflectionReader($this->makeService($selector, PropertySelectorInterface::class));
        }

        $readers = [
            ...$this->makeServices($settings->getList('metadata.readers'), MetadataReaderInterface::class),
            ...$this->createFileReaders($settings->getList('metadata.paths')),
        ];

        foreach (\array_reverse($readers) as $reader) {
            $builder = $builder->withAddedReader($reader, \PHP_INT_MAX);
        }

        if (!$this->isCacheEnabled($settings)) {
            return $builder->build();
        }

        $cache = $this->makeService('cache', CacheFactory::class);

        $stores = $settings->getCacheStores();

        if ($stores === []) {
            $stores = [['store' => null, 'prefix' => null]];
        }

        foreach ($stores as $store) {
            $builder = $builder->withPsr16Cache(
                $cache->store($store['store']),
                $store['prefix'] ?? CacheMetadataProvider::DEFAULT_PREFIX . $name . '_',
            );
        }

        return $builder->build();
    }

    /**
     * @throws InvalidConfigException in case of the setting is invalid
     */
    private function isCacheEnabled(MapperSettings $settings): bool
    {
        return $settings->findBool('metadata.cache.enabled') ?? !$this->isDebug;
    }

    /**
     * @param non-empty-string $name
     * @throws InvalidConfigException in case of the service is invalid
     */
    private function createNamingStrategy(string $name): NamingStrategyInterface
    {
        $class = self::NAMING_STRATEGIES[$name] ?? null;

        if ($class !== null) {
            return new $class();
        }

        return $this->makeService($name, NamingStrategyInterface::class);
    }

    /**
     * Creates a reader per mapping config file, in the order of the paths,
     * the files of a directory by the name.
     *
     * @param list<non-empty-string> $paths
     * @return list<FileMetadataReader>
     * @throws InvalidConfigException in case of a path does not exist or its
     *         format is not supported
     */
    private function createFileReaders(array $paths): array
    {
        $result = [];

        foreach ($paths as $path) {
            foreach (self::findConfigFiles($path) as $file) {
                $class = self::getFileReaderClass($file);

                $result[] = new $class($file, $this->parser);
            }
        }

        return $result;
    }

    /**
     * @param non-empty-string $path
     * @return list<non-empty-string>
     * @throws InvalidConfigException in case of the path does not exist
     */
    private static function findConfigFiles(string $path): array
    {
        if (!\file_exists($path)) {
            throw InvalidConfigException::becausePathDoesNotExist($path);
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
     * @throws InvalidConfigException in case of the format of the file is
     *         not supported
     */
    private static function getFileReaderClass(string $file): string
    {
        $extension = \strtolower(\pathinfo($file, \PATHINFO_EXTENSION));

        return self::FILE_READERS[$extension]
            ?? throw InvalidConfigException::becauseFileIsNotSupported($file, \array_keys(self::FILE_READERS));
    }

    /**
     * @return array{
     *    int?: CoercerInterface<int>,
     *    float?: CoercerInterface<float>,
     *    string?: CoercerInterface<string>,
     *    bool?: CoercerInterface<bool>,
     *    array?: CoercerInterface<array<array-key, mixed>>,
     * }
     * @throws InvalidConfigException in case of a coercer is invalid
     */
    private function makeCoercers(MapperSettings $settings): array
    {
        $result = [];

        foreach ($settings->getCoercers() as $type => $service) {
            $result[$type] = $this->makeService($service, CoercerInterface::class);
        }

        return $result;
    }

    /**
     * @return array<value-of<MapperSettings::FORMATTED_DIRECTIONS>, ViolationFormatterInterface>
     * @throws InvalidConfigException in case of a formatter is invalid
     */
    private function makeFormatters(MapperSettings $settings): array
    {
        $result = [];

        foreach ($settings->getFormatters() as $direction => $service) {
            $result[$direction] = $this->makeService($service, ViolationFormatterInterface::class);
        }

        return $result;
    }

    /**
     * @template TArg of object
     * @param list<non-empty-string> $ids
     * @param class-string<TArg> $class
     * @return list<TArg>
     * @throws InvalidConfigException in case of a service does not implement
     *         the class
     */
    private function makeServices(array $ids, string $class): array
    {
        $result = [];

        foreach ($ids as $id) {
            $result[] = $this->makeService($id, $class);
        }

        return $result;
    }

    /**
     * @template TArg of object
     * @param non-empty-string $id
     * @param class-string<TArg> $class
     * @return TArg
     * @throws InvalidConfigException in case of the service does not
     *         implement the class
     */
    private function makeService(string $id, string $class): object
    {
        $service = $this->container->make($id);

        if ($service instanceof $class) {
            return $service;
        }

        throw InvalidConfigException::becauseServiceIsInvalid($id, $class, $service);
    }
}
