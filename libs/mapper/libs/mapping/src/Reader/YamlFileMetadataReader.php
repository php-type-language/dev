<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use TypeLang\Mapper\Mapping\Exception\ConfigLoadingException;
use TypeLang\Mapper\Mapping\Exception\MissingDependencyException;

/**
 * Overrides values of the given properties with a config stored in a YAML
 * file, see {@see FileMetadataReader}. Requires the `symfony/yaml` package.
 */
final class YamlFileMetadataReader extends FileMetadataReader
{
    /**
     * @param non-empty-string $path
     * @throws MissingDependencyException in case of the `symfony/yaml`
     *         package is not installed
     */
    public function __construct(string $path)
    {
        if (!\class_exists(Yaml::class)) {
            throw MissingDependencyException::becausePackageIsNotInstalled('symfony/yaml', 'YAML config');
        }

        parent::__construct($path);
    }

    protected function decode(): mixed
    {
        try {
            return Yaml::parseFile($this->path);
        } catch (ParseException $e) {
            throw ConfigLoadingException::becauseContentIsInvalid($this->path, $e);
        }
    }
}
