<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader;

use TypeLang\Mapper\Mapping\Reader\Internal\SourceContext;

/**
 * Config of one property: its keys and the places of their values.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class PropertyConfig
{
    /**
     * @param non-empty-string $target property reference: `App\User::$id`
     * @param array<array-key, mixed> $values
     */
    private function __construct(
        private readonly string $target,
        private readonly array $values,
        private readonly ConfigPath $path,
        /**
         * Whether the config is one value written in place of the map, its
         * key has the place of the property
         */
        private readonly bool $isShorthand,
    ) {}

    /**
     * Creates the config written as a map: `id: {type: int}`.
     *
     * @param non-empty-string $target property reference: `App\User::$id`
     * @param array<array-key, mixed> $values
     * @param ConfigPath $path path of the property
     */
    public static function createFromMap(string $target, array $values, ConfigPath $path): self
    {
        return new self($target, $values, $path, false);
    }

    /**
     * Creates the config written as one value of the key: `id: int`.
     *
     * @param non-empty-string $target property reference: `App\User::$id`
     * @param non-empty-string $key
     * @param ConfigPath $path path of the property
     */
    public static function createFromValue(string $target, string $key, mixed $value, ConfigPath $path): self
    {
        return new self($target, [$key => $value], $path, true);
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->values);
    }

    /**
     * Returns the value of the key, {@see null} in case of there is no key.
     */
    public function get(string $key): mixed
    {
        return $this->values[$key] ?? null;
    }

    /**
     * Returns the place of the value of the key.
     *
     * @param non-empty-string $key
     */
    public function createContext(string $key): SourceContext
    {
        $path = $this->path;

        if (!$this->isShorthand) {
            $path = $path->withKey($key);
        }

        return new SourceContext($this->target, $path->createSource());
    }
}
