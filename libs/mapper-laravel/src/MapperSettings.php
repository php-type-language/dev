<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel;

use TypeLang\Mapper\Bridge\Laravel\Exception\InvalidConfigException;

/**
 * Settings of a mapper from the `type-lang-mapper` config, the ones left
 * {@see null} are inherited from the default mapper.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Bridge\Laravel
 */
final class MapperSettings
{
    /**
     * Types of the coercers, the arguments of
     * {@see \TypeLang\Mapper\Context\Coercers::createDefault()}
     */
    public const COERCED_TYPES = ['int', 'float', 'string', 'bool', 'array'];

    public function __construct(
        /**
         * Prefix of the keys of the settings in the messages of the errors
         *
         * @var non-empty-string
         */
        private readonly string $key,
        /**
         * @var array<array-key, mixed>
         */
        private readonly array $own,
        /**
         * Settings of the default mapper inherited by a named one
         *
         * @var array<array-key, mixed>|null
         */
        private readonly ?array $inherited = null,
    ) {}

    /**
     * @param non-empty-string $key dot separated path of the setting
     * @throws InvalidConfigException in case of the setting is not a boolean
     */
    public function findBool(string $key): ?bool
    {
        $value = $this->findValue($key);

        if ($value === null || \is_bool($value)) {
            return $value;
        }

        throw InvalidConfigException::becauseValueIsInvalid($this->getKey($key), 'a boolean', $value);
    }

    /**
     * @param non-empty-string $key dot separated path of the setting
     * @return int<1, max>|null
     * @throws InvalidConfigException in case of the setting is not a
     *         positive integer
     */
    public function findPositiveInt(string $key): ?int
    {
        $value = $this->findValue($key);

        if ($value === null || (\is_int($value) && $value > 0)) {
            return $value;
        }

        throw InvalidConfigException::becauseValueIsInvalid($this->getKey($key), 'an integer of at least 1', $value);
    }

    /**
     * @param non-empty-string $key dot separated path of the setting
     * @return int<0, max>|null
     * @throws InvalidConfigException in case of the setting is not a
     *         non-negative integer
     */
    public function findNonNegativeInt(string $key): ?int
    {
        $value = $this->findValue($key);

        if ($value === null || (\is_int($value) && $value >= 0)) {
            return $value;
        }

        throw InvalidConfigException::becauseValueIsInvalid($this->getKey($key), 'an integer of at least 0', $value);
    }

    /**
     * @param non-empty-string $key dot separated path of the setting
     * @return non-empty-string|null
     * @throws InvalidConfigException in case of the setting is not a
     *         non-empty string
     */
    public function findString(string $key): ?string
    {
        $value = $this->findValue($key);

        if ($value === null || (\is_string($value) && $value !== '')) {
            return $value;
        }

        throw InvalidConfigException::becauseValueIsInvalid($this->getKey($key), 'a non-empty string', $value);
    }

    /**
     * Returns the list of the mapper followed by the inherited one.
     *
     * @param non-empty-string $key dot separated path of the setting
     * @return list<non-empty-string>
     * @throws InvalidConfigException in case of the setting is not a list of
     *         non-empty strings
     */
    public function getList(string $key): array
    {
        $result = [
            ...$this->readList($key, self::read($this->own, $key)),
            ...$this->readList($key, self::read($this->inherited ?? [], $key)),
        ];

        return \array_values(\array_unique($result));
    }

    /**
     * Returns the services of the coercers by the type, the ones of the
     * mapper replace the inherited ones.
     *
     * @return array<value-of<self::COERCED_TYPES>, non-empty-string>
     * @throws InvalidConfigException in case of a type is not supported or a
     *         service is not a non-empty string
     */
    public function getCoercers(): array
    {
        return [
            ...$this->readCoercers(self::read($this->inherited ?? [], 'coercers')),
            ...$this->readCoercers(self::read($this->own, 'coercers')),
        ];
    }

    /**
     * Returns the cache stores of the metadata, the ones of the mapper
     * replace the inherited ones, a {@see null} store is the default one.
     *
     * @return list<array{store: non-empty-string|null, prefix: string|null}>
     * @throws InvalidConfigException in case of a store is written wrong
     */
    public function getCacheStores(): array
    {
        $key = 'metadata.cache.stores';
        $stores = self::read($this->own, $key) ?? [];

        if ($stores === []) {
            $stores = self::read($this->inherited ?? [], $key) ?? [];
        }

        if (!\is_array($stores) || !\array_is_list($stores)) {
            throw InvalidConfigException::becauseValueIsInvalid($this->getKey($key), 'a list', $stores);
        }

        $result = [];

        foreach ($stores as $store) {
            if (\is_string($store) && $store !== '') {
                $store = ['store' => $store];
            }

            if (!\is_array($store)) {
                throw InvalidConfigException::becauseValueIsInvalid(
                    $this->getKey($key),
                    'a list of store names or arrays',
                    $store,
                );
            }

            $name = $store['store'] ?? null;
            $prefix = $store['prefix'] ?? null;

            if (($name !== null && (!\is_string($name) || $name === '')) || ($prefix !== null && !\is_string($prefix))) {
                throw InvalidConfigException::becauseValueIsInvalid(
                    $this->getKey($key),
                    'a list of arrays with the string "store" and "prefix"',
                    $store,
                );
            }

            $result[] = ['store' => $name, 'prefix' => $prefix];
        }

        return $result;
    }

    /**
     * @param non-empty-string $key
     */
    private function findValue(string $key): mixed
    {
        return self::read($this->own, $key) ?? self::read($this->inherited ?? [], $key);
    }

    /**
     * @param array<array-key, mixed> $settings
     * @param non-empty-string $key
     */
    private static function read(array $settings, string $key): mixed
    {
        $result = $settings;

        foreach (\explode('.', $key) as $segment) {
            if (!\is_array($result)) {
                return null;
            }

            $result = $result[$segment] ?? null;
        }

        return $result;
    }

    /**
     * @param non-empty-string $key
     * @return list<non-empty-string>
     * @throws InvalidConfigException in case of the value is not a list of
     *         non-empty strings
     */
    private function readList(string $key, mixed $value): array
    {
        if ($value === null) {
            return [];
        }

        if (!\is_array($value) || !\array_is_list($value)) {
            throw InvalidConfigException::becauseValueIsInvalid($this->getKey($key), 'a list', $value);
        }

        $result = [];

        foreach ($value as $item) {
            if (!\is_string($item) || $item === '') {
                throw InvalidConfigException::becauseValueIsInvalid(
                    $this->getKey($key),
                    'a list of non-empty strings',
                    $item,
                );
            }

            $result[] = $item;
        }

        return $result;
    }

    /**
     * @return array<value-of<self::COERCED_TYPES>, non-empty-string>
     * @throws InvalidConfigException in case of a type is not supported or a
     *         service is not a non-empty string
     */
    private function readCoercers(mixed $value): array
    {
        if ($value === null) {
            return [];
        }

        if (!\is_array($value)) {
            throw InvalidConfigException::becauseValueIsInvalid($this->getKey('coercers'), 'an array', $value);
        }

        $result = [];

        foreach ($value as $type => $service) {
            if (!\in_array($type, self::COERCED_TYPES, true)) {
                throw InvalidConfigException::becauseCoercedTypeIsUnknown((string) $type, self::COERCED_TYPES);
            }

            if ($service === null) {
                continue;
            }

            if (!\is_string($service) || $service === '') {
                throw InvalidConfigException::becauseValueIsInvalid(
                    $this->getKey('coercers.' . $type),
                    'a non-empty string',
                    $service,
                );
            }

            $result[$type] = $service;
        }

        return $result;
    }

    /**
     * @param non-empty-string $key
     * @return non-empty-string
     */
    private function getKey(string $key): string
    {
        return $this->key . '.' . $key;
    }
}
