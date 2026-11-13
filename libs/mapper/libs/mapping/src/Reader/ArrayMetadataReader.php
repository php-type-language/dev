<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader;

use TypeLang\Mapper\Mapping\Exception\ConfigLoadingException;
use TypeLang\Mapper\Mapping\Exception\InvalidMetadataException;
use TypeLang\Mapper\Mapping\Exception\InvalidValueException;
use TypeLang\Mapper\Mapping\Exception\MetadataException;
use TypeLang\Mapper\Mapping\Exception\UnknownKeyException;
use TypeLang\Mapper\Mapping\Exception\UnknownPropertyException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\ClassIndex;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\ConfigPath;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\DefaultConfigLoader;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\NameConfigLoader;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\PropertyConfig;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\PropertyConfigLoaderInterface;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\TypeConfigLoader;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\TypeConfigLoader\TypeDirection;
use TypeLang\Mapper\Mapping\Reader\Internal\ErrorCollector;
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeParserInterface;
use TypeLang\Type\TypeNode;

/**
 * Overrides values of the given properties with a config: a map of class
 * names to class configs. A config of a property that is not given is an
 * error. Types are kept as written, names in them are fully qualified.
 * Class names are case-insensitive, the leading `\` is optional.
 *
 * ```
 * App\User:
 *   properties:
 *     id: int
 *     email:
 *       name: e_mail
 *       type: non-empty-string
 *       read_type: string
 *       write_type: non-empty-string
 *       strict: true
 *       default: null
 * ```
 */
final class ArrayMetadataReader implements MetadataReaderInterface
{
    private const PROPERTIES_KEY = 'properties';

    /**
     * @var non-empty-list<PropertyConfigLoaderInterface>
     */
    private readonly array $loaders;

    /**
     * Keys of a property config read by the loaders
     *
     * @var array<non-empty-string, true>
     */
    private readonly array $keys;

    private ?ClassIndex $index = null;

    /**
     * @param TypeParserInterface<TypeNode, string> $parser parser of the
     *        types of the config
     */
    public function __construct(
        /**
         * @var array<array-key, mixed>
         */
        private readonly array $config,
        /**
         * File of the config for the place of errors, {@see null} in case
         * of the config has none
         *
         * @var non-empty-string|null
         */
        private readonly ?string $file = null,
        TypeParserInterface $parser = new TypeParser(),
    ) {
        $this->loaders = [
            new TypeConfigLoader($parser),
            new NameConfigLoader(),
            new DefaultConfigLoader(),
        ];

        $keys = [];

        foreach ($this->loaders as $loader) {
            foreach ($loader->getKeys() as $key) {
                $keys[$key] = true;
            }
        }

        $this->keys = $keys;
    }

    /**
     * @throws ConfigLoadingException in case of the config is not a map of
     *         classes
     * @throws InvalidMetadataException in case of the config of the class
     *         is wrong
     */
    public function read(\ReflectionClass $class, ?ClassMetadata $metadata = null): ClassMetadata
    {
        $metadata ??= new ClassMetadata($class->getName());
        $this->index ??= new ClassIndex($this->config, $this->file);
        $key = $this->index->findKey($class);

        if ($key === null) {
            return $metadata;
        }

        $errors = new ErrorCollector();

        $metadata = $this->readClass($class, $metadata, $key, $errors);

        $errors->throwIfNotEmpty($metadata);

        return $metadata;
    }

    /**
     * @template T of object
     * @param \ReflectionClass<T> $class
     * @param ClassMetadata<T> $metadata
     * @param non-empty-string $key
     * @return ClassMetadata<T>
     */
    private function readClass(
        \ReflectionClass $class,
        ClassMetadata $metadata,
        string $key,
        ErrorCollector $errors,
    ): ClassMetadata {
        $config = $this->config[$key];
        $path = ConfigPath::createRoot($this->file)->withKey($key);

        if (!\is_array($config)) {
            $errors->addError(InvalidValueException::becauseClassConfigIsNotMap(
                $class->name,
                $path->createSource(),
            ));

            return $metadata;
        }

        if (\array_key_exists(self::PROPERTIES_KEY, $config)) {
            $metadata = $this->readProperties(
                class: $class,
                metadata: $metadata,
                properties: $config[self::PROPERTIES_KEY],
                path: $path->withKey(self::PROPERTIES_KEY),
                errors: $errors,
            );
        }

        foreach ($config as $name => $_) {
            if ($name !== self::PROPERTIES_KEY) {
                $errors->addError(UnknownKeyException::becauseKeyIsUnknown(
                    $name,
                    $class->name,
                    $path->withKey($name)->createSource(),
                ));
            }
        }

        return $metadata;
    }

    /**
     * @template T of object
     * @param \ReflectionClass<T> $class
     * @param ClassMetadata<T> $metadata
     * @return ClassMetadata<T>
     */
    private function readProperties(
        \ReflectionClass $class,
        ClassMetadata $metadata,
        mixed $properties,
        ConfigPath $path,
        ErrorCollector $errors,
    ): ClassMetadata {
        if (!\is_array($properties)) {
            $errors->addError(InvalidValueException::becauseValueIsNotMap(
                self::PROPERTIES_KEY,
                $class->name,
                $path->createSource(),
            ));

            return $metadata;
        }

        foreach ($properties as $name => $config) {
            $name = (string) $name;
            $target = MetadataException::formatPropertyTarget($class->name, $name);
            $propertyPath = $path->withKey($name);

            if ($name === '' || !$class->hasProperty($name)) {
                $errors->addError(UnknownPropertyException::becausePropertyDoesNotExist(
                    $target,
                    $propertyPath->createSource(),
                ));

                continue;
            }

            $property = $metadata->findProperty($name);

            if ($property === null) {
                $errors->addError(UnknownPropertyException::becausePropertyIsNotMapped(
                    $target,
                    $propertyPath->createSource(),
                ));

                continue;
            }

            $property = $this->readProperty($config, $property, $target, $propertyPath, $errors);

            $metadata = $metadata->withProperty($property);
        }

        return $metadata;
    }

    /**
     * @param non-empty-string $target property reference: `App\User::$id`
     * @param ConfigPath $path path of the property
     */
    private function readProperty(
        mixed $config,
        PropertyMetadata $property,
        string $target,
        ConfigPath $path,
        ErrorCollector $errors,
    ): PropertyMetadata {
        // id: int
        if (\is_string($config)) {
            $config = PropertyConfig::createFromValue($target, TypeDirection::Both->value, $config, $path);

            return $this->loadProperty($config, $property, $errors);
        }

        if (!\is_array($config)) {
            $errors->addError(InvalidValueException::becausePropertyConfigIsNotStringOrMap(
                $target,
                $path->createSource(),
            ));

            return $property;
        }

        $property = $this->loadProperty(
            config: PropertyConfig::createFromMap($target, $config, $path),
            property: $property,
            errors: $errors,
        );

        foreach ($config as $key => $_) {
            if (!isset($this->keys[$key])) {
                $errors->addError(UnknownKeyException::becauseKeyIsUnknown(
                    $key,
                    $target,
                    $path->withKey($key)->createSource(),
                ));
            }
        }

        return $property;
    }

    /**
     * Applies each loader to the config, an error of a loader is collected
     * and the other loaders go on.
     */
    private function loadProperty(
        PropertyConfig $config,
        PropertyMetadata $property,
        ErrorCollector $errors,
    ): PropertyMetadata {
        foreach ($this->loaders as $loader) {
            try {
                $property = $loader->load($config, $property);
            } catch (MetadataException $e) {
                $errors->addError($e);
            }
        }

        return $property;
    }
}
