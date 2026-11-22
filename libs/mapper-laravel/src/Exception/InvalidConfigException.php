<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel\Exception;

/**
 * The `type-lang-mapper` config is invalid.
 */
final class InvalidConfigException extends \InvalidArgumentException
{
    /**
     * @param non-empty-string $pattern
     */
    public static function becauseMapperNameIsInvalid(string $name, string $pattern, string $default): self
    {
        return new self(\sprintf(
            'Mapper name "%s" is invalid, a name matches "%s" and is not "%s"',
            \addcslashes($name, '"'),
            $pattern,
            $default,
        ));
    }

    /**
     * @param non-empty-string $key
     * @param non-empty-string $expected
     */
    public static function becauseValueIsInvalid(string $key, string $expected, mixed $value): self
    {
        return new self(\sprintf(
            'Config "%s" must be %s, but %s given',
            $key,
            $expected,
            \get_debug_type($value),
        ));
    }

    /**
     * @param list<non-empty-string> $supported
     */
    public static function becauseKeyIsUnknown(string $key, array $supported): self
    {
        return new self(\sprintf(
            'Config "%s" is not supported, supported keys are %s',
            \addcslashes($key, '"'),
            \implode(', ', $supported),
        ));
    }

    /**
     * @param class-string $class
     */
    public static function becauseServiceIsInvalid(string $id, string $class, mixed $service): self
    {
        return new self(\sprintf(
            'Service "%s" must implement "%s", but %s given',
            \addcslashes($id, '"'),
            $class,
            \get_debug_type($service),
        ));
    }

    public static function becausePathDoesNotExist(string $path): self
    {
        return new self(\sprintf('Mapping config path "%s" does not exist', \addcslashes($path, '"')));
    }

    /**
     * @param list<non-empty-string> $supported
     */
    public static function becauseFileIsNotSupported(string $file, array $supported): self
    {
        return new self(\sprintf(
            'Mapping config file "%s" is not supported, supported extensions are %s',
            \addcslashes($file, '"'),
            \implode(', ', $supported),
        ));
    }

    public static function becauseProfilerRequiresPackage(string $key, string $package): self
    {
        return new self(\sprintf('Config "%s" requires the "%s" package', $key, $package));
    }
}
