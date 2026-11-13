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
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\DefaultKeyLoader;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\KeyLoaderInterface;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\NameKeyLoader;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\TypeKeyLoader;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader\TypeKeyLoader\TypeDirection;
use TypeLang\Mapper\Mapping\Reader\Internal\ErrorCollector;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContext;
use TypeLang\Mapper\Mapping\Reader\Internal\SourceContextFactory;
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

    private const STRICT_KEY = 'strict';

    /**
     * @var array<non-empty-string, KeyLoaderInterface>
     */
    private readonly array $loaders;

    private readonly SourceContextFactory $contexts;

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
        $this->contexts = new SourceContextFactory();
        $this->loaders = [
            TypeDirection::Both->value => new TypeKeyLoader(TypeDirection::Both, $parser),
            TypeDirection::Read->value => new TypeKeyLoader(TypeDirection::Read, $parser),
            TypeDirection::Write->value => new TypeKeyLoader(TypeDirection::Write, $parser),
            'name' => new NameKeyLoader(),
            'default' => new DefaultKeyLoader(),
        ];
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
        $path = ConfigPath::createRoot()->withKey($key);

        if (!\is_array($config)) {
            $errors->addError(InvalidValueException::becauseClassConfigIsNotMap(
                $class->name,
                $path->createSource($this->file),
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
                    $path->withKey($name)->createSource($this->file),
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
                $path->createSource($this->file),
            ));

            return $metadata;
        }

        foreach ($properties as $name => $config) {
            $name = (string) $name;
            $target = MetadataException::formatPropertyTarget($class->name, $name);
            $propertyPath = $path->withKey($name);
            $context = $this->contexts->createFromConfig(
                $target,
                $propertyPath->createSource($this->file),
            );

            if ($name === '' || !$class->hasProperty($name)) {
                $errors->addError(UnknownPropertyException::becausePropertyDoesNotExist(
                    $target,
                    $context->source,
                ));

                continue;
            }

            $property = $metadata->findProperty($name);

            if ($property === null) {
                $errors->addError(UnknownPropertyException::becausePropertyIsNotMapped(
                    $target,
                    $context->source,
                ));

                continue;
            }

            $property = $this->readProperty($config, $property, $propertyPath, $context, $errors);

            $metadata = $metadata->withProperty($property);
        }

        return $metadata;
    }

    private function readProperty(
        mixed $config,
        PropertyMetadata $property,
        ConfigPath $path,
        SourceContext $context,
        ErrorCollector $errors,
    ): PropertyMetadata {
        // id: int
        if (\is_string($config)) {
            return $this->loadKey(TypeDirection::Both->value, $config, $property, $path, $context, $errors);
        }

        if (!\is_array($config)) {
            $errors->addError(InvalidValueException::becausePropertyConfigIsNotStringOrMap(
                $context->target,
                $context->source,
            ));

            return $property;
        }

        foreach ($this->loaders as $key => $_) {
            if (\array_key_exists($key, $config)) {
                $property = $this->loadKey($key, $config[$key], $property, $path->withKey($key), $context, $errors);
            }
        }

        if (\array_key_exists(self::STRICT_KEY, $config)) {
            $property = $this->loadStrict($config, $property, $path->withKey(self::STRICT_KEY), $context, $errors);
        }

        foreach ($config as $key => $_) {
            if (!isset($this->loaders[$key]) && $key !== self::STRICT_KEY) {
                $errors->addError(UnknownKeyException::becauseKeyIsUnknown(
                    $key,
                    $context->target,
                    $path->withKey($key)->createSource($this->file),
                ));
            }
        }

        return $property;
    }

    /**
     * @param non-empty-string $key
     * @param ConfigPath $path path of the value
     */
    private function loadKey(
        string $key,
        mixed $value,
        PropertyMetadata $property,
        ConfigPath $path,
        SourceContext $context,
        ErrorCollector $errors,
    ): PropertyMetadata {
        $source = $path->createSource($this->file);

        try {
            return $this->loaders[$key]->load(
                $value,
                $property,
                $this->contexts->createFromConfig($context->target, $source),
            );
        } catch (MetadataException $e) {
            $errors->addError($e);
        }

        return $property;
    }

    /**
     * Applies the `strict` key to the types the config sets, or to the types
     * of the lower readers in case of the config sets no type.
     *
     * @param array<array-key, mixed> $config
     * @param ConfigPath $path path of the value
     */
    private function loadStrict(
        array $config,
        PropertyMetadata $property,
        ConfigPath $path,
        SourceContext $context,
        ErrorCollector $errors,
    ): PropertyMetadata {
        $isStrict = $config[self::STRICT_KEY];

        if (!\is_bool($isStrict)) {
            $errors->addError(InvalidValueException::becauseValueIsNotBoolean(
                self::STRICT_KEY,
                $context->target,
                $path->createSource($this->file),
            ));

            return $property;
        }

        $isRead = false;
        $isWrite = false;

        foreach (TypeDirection::cases() as $direction) {
            if (\array_key_exists($direction->value, $config)) {
                $isRead = $isRead || $direction->isRead();
                $isWrite = $isWrite || $direction->isWrite();
            }
        }

        // strict: true
        if (!$isRead && !$isWrite) {
            $isRead = true;
            $isWrite = true;
        }

        if ($isRead && $property->read !== null) {
            $property = $property->withReadType($property->read->withStrict($isStrict));
        }

        if ($isWrite && $property->write !== null) {
            $property = $property->withWriteType($property->write->withStrict($isStrict));
        }

        return $property;
    }
}
