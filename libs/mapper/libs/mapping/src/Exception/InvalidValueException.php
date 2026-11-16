<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Exception;

use TypeLang\Mapper\Mapping\Metadata\SourceInfo;

/**
 * Value of an attribute argument or of a config key has a wrong type.
 */
final class InvalidValueException extends MetadataException
{
    /**
     * @param string $target property or class: `App\User::$id`, `App\User`
     */
    public static function becauseValueIsNotNonEmptyString(string $key, string $target, SourceInfo $source): self
    {
        return new self($target, $source, self::formatValue($key, 'a non-empty string'));
    }

    /**
     * @param string $target property or class: `App\User::$id`, `App\User`
     */
    public static function becauseValueIsNotBoolean(string $key, string $target, SourceInfo $source): self
    {
        return new self($target, $source, self::formatValue($key, 'a boolean'));
    }

    /**
     * @param string $target property or class: `App\User::$id`, `App\User`
     */
    public static function becauseValueIsNotMap(string $key, string $target, SourceInfo $source): self
    {
        return new self($target, $source, self::formatValue($key, 'a map'));
    }

    /**
     * @param string $target property or class: `App\User::$id`, `App\User`
     */
    public static function becauseValueIsNotNonEmptyMap(string $key, string $target, SourceInfo $source): self
    {
        return new self($target, $source, self::formatValue($key, 'a non-empty map'));
    }

    /**
     * @param string $target property or class: `App\User::$id`, `App\User`
     */
    public static function becauseExpressionIsInvalid(
        string $expression,
        string $target,
        SourceInfo $source,
        \Throwable $previous,
    ): self {
        $message = \sprintf(
            'expression "%s" is invalid (%s)',
            \addcslashes($expression, '"'),
            $previous->getMessage(),
        );

        return new self($target, $source, $message, $previous);
    }

    public static function becauseClassConfigIsNotMap(string $class, SourceInfo $source): self
    {
        return new self($class, $source, 'config must be a map');
    }

    /**
     * @param string $property property reference: `App\User::$id`
     */
    public static function becausePropertyConfigIsNotStringOrMap(string $property, SourceInfo $source): self
    {
        return new self($property, $source, 'config must be a string or a map');
    }

    private static function formatValue(string $key, string $expected): string
    {
        return \sprintf('value of "%s" must be %s', \addcslashes($key, '"'), $expected);
    }
}
