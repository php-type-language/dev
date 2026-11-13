<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader;

use TypeLang\Mapper\Mapping\Metadata\SourceInfo;

/**
 * Place in a config, printed as JSONPath: `$['App\User'].properties.id.type`.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class ConfigPath
{
    /**
     * @param non-empty-string $path
     */
    private function __construct(
        private readonly string $path,
    ) {}

    /**
     * Returns the path of the config itself: `$`.
     */
    public static function createRoot(): self
    {
        return new self('$');
    }

    /**
     * Returns the path of the child key.
     */
    public function withKey(int|string $key): self
    {
        if (\is_int($key)) {
            return new self($this->path . '[' . $key . ']');
        }

        if (\preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $key) === 1) {
            return new self($this->path . '.' . $key);
        }

        return new self($this->path . '[\'' . \addcslashes($key, '\'') . '\']');
    }

    /**
     * Returns the source of the values of this place.
     *
     * @param non-empty-string|null $file
     */
    public function createSource(?string $file): SourceInfo
    {
        return new SourceInfo($file, $this->path);
    }
}
