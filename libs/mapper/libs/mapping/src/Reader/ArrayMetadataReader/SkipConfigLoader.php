<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader;

use TypeLang\Mapper\Mapping\Exception\InvalidValueException;
use TypeLang\Mapper\Mapping\Exception\MissingDependencyException;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\Metadata\SkipCondition\EmptySkipCondition;
use TypeLang\Mapper\Mapping\Metadata\SkipCondition\NullSkipCondition;
use TypeLang\Mapper\Mapping\Reader\Internal\SkipConditionFactory;

/**
 * Sets the skip conditions from the `skip` key: one condition or a list of
 * them, `null`, `empty` or an expression over the object `this`.
 *
 * ```
 * skip: [null, 'this.isPrivate()']
 * ```
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class SkipConfigLoader implements PropertyConfigLoaderInterface
{
    private const KEY = 'skip';

    public function __construct(
        private readonly SkipConditionFactory $conditions,
    ) {}

    public function getKeys(): array
    {
        return [self::KEY];
    }

    /**
     * @throws InvalidValueException in case of a condition is not a
     *         non-empty string or its expression cannot be parsed
     * @throws MissingDependencyException in case of an expression is given,
     *         but the `symfony/expression-language` package is not installed
     */
    public function load(PropertyConfig $config, PropertyMetadata $property): PropertyMetadata
    {
        if (!$config->has(self::KEY)) {
            return $property;
        }

        $context = $config->createContext(self::KEY);
        $values = $config->get(self::KEY);

        if (!\is_array($values)) {
            $values = [$values];
        }

        $conditions = [];

        foreach ($values as $value) {
            if (!\is_string($value) || $value === '') {
                throw InvalidValueException::becauseValueIsNotNonEmptyString(
                    self::KEY,
                    $context->target,
                    $context->source,
                );
            }

            $conditions[] = match ($value) {
                'null' => new NullSkipCondition(),
                'empty' => new EmptySkipCondition(),
                default => $this->conditions->createExpression($value, 'this', $context),
            };
        }

        return $property->withSkipConditions($conditions);
    }
}
