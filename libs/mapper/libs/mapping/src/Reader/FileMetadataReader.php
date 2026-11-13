<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader;

use TypeLang\Mapper\Mapping\Exception\ConfigLoadingException;
use TypeLang\Mapper\Mapping\Exception\InvalidMetadataException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;

/**
 * Overrides values of the given properties with a config stored in a file,
 * see {@see ArrayMetadataReader}.
 */
abstract class FileMetadataReader implements MetadataReaderInterface
{
    private ?ArrayMetadataReader $reader = null;

    public function __construct(
        /**
         * @var non-empty-string
         */
        protected readonly string $path,
    ) {}

    /**
     * @throws ConfigLoadingException in case of the file cannot be read or
     *         is not a map of classes
     * @throws InvalidMetadataException in case of the config of the class
     *         is wrong
     */
    public function read(\ReflectionClass $class, ?ClassMetadata $metadata = null): ClassMetadata
    {
        $this->reader ??= new ArrayMetadataReader($this->load(), $this->path);

        return $this->reader->read($class, $metadata);
    }

    /**
     * Decodes the content of the readable file, returns the config.
     *
     * @throws ConfigLoadingException in case of the content cannot be
     *         decoded
     */
    abstract protected function decode(): mixed;

    /**
     * @return array<array-key, mixed>
     * @throws ConfigLoadingException in case of the file cannot be read or
     *         decoded
     */
    private function load(): array
    {
        if (!\is_file($this->path) || !\is_readable($this->path)) {
            throw ConfigLoadingException::becauseFileIsNotReadable($this->path);
        }

        $config = $this->decode();

        if (!\is_array($config)) {
            throw ConfigLoadingException::becauseContentIsNotMap($this->path);
        }

        return $config;
    }
}
