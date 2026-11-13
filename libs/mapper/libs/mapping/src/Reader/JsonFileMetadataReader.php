<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader;

use TypeLang\Mapper\Mapping\Exception\ConfigLoadingException;

/**
 * Overrides values of the given properties with a config stored in a JSON
 * file, see {@see FileMetadataReader}.
 */
final class JsonFileMetadataReader extends FileMetadataReader
{
    protected function decode(): mixed
    {
        $json = \file_get_contents($this->path);

        if ($json === false) {
            throw ConfigLoadingException::becauseFileIsNotReadable($this->path);
        }

        try {
            return \json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw ConfigLoadingException::becauseContentIsInvalid($this->path, $e);
        }
    }
}
