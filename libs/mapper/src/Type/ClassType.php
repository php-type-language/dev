<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Type;

use TypeLang\Mapper\Context\Context;
use TypeLang\Mapper\Diagnostics\Path\Segment\PropertySegment;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Exception\NotInterceptableExceptionInterface;
use TypeLang\Mapper\Exception\TypeDefinitionException;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Type\ClassType\Property;

/**
 * Object of a class: created and filled from an array or a
 * {@see \stdClass} on denormalization, turned into an array of its
 * properties on normalization. A {@see \stdClass} is accepted in the strict
 * context too.
 *
 * The type holds the plan only: the metadata and the types of the
 * properties of each direction. The instantiator of the class, the readers
 * and writers of the properties and the hooks of the class are taken from
 * {@see Context::$accessors} by the metadata. The hooks before a conversion
 * run in its check too.
 *
 * A property that cannot be read on normalization, such as an uninitialized
 * one, is a fault of the object: its {@see \Error} becomes a violation with
 * the path of the property. A property whose skip condition is matched by
 * its value is left out of the data and is not checked.
 *
 * @template T of object
 *
 * @template-implements TypeInterface<T>
 */
final class ClassType implements TypeInterface
{
    public function __construct(
        /**
         * Description of the class the type is built from
         *
         * @var ClassMetadata<T>
         */
        public readonly ClassMetadata $metadata,
        /**
         * Properties filled on denormalization, with the types of the
         * written values
         *
         * @var list<Property<mixed>>
         */
        public readonly array $writable,
        /**
         * Properties taken on normalization, with the types of the read
         * values
         *
         * @var list<Property<mixed>>
         */
        public readonly array $readable,
    ) {}

    public function isDenormalizable(mixed $value, Context $context): bool
    {
        foreach ($context->accessors->get($this->metadata)->beforeDenormalization as $hook) {
            $value = $hook->beforeDenormalize($this->metadata, $value, $context);
        }

        $value = $context->coercers->array->coerce($value);

        if ($value === null) {
            return false;
        }

        foreach ($this->writable as $property) {
            $key = $property->metadata->externalName;

            if (!\array_key_exists($key, $value)) {
                if ($property->metadata->default !== null) {
                    continue;
                }

                return false;
            }

            $isStrict = $property->metadata->write?->isStrict;
            $current = $isStrict === null ? $context : $context->withStrictTypes($isStrict);

            if (!$property->type->isDenormalizable($value[$key], $current)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return T
     * @throws InvalidValueException in case of the value or one of its
     *         fields cannot be denormalized
     * @throws TypeDefinitionException in case of a strategy rejects the
     *         class or a property
     */
    public function denormalize(mixed $value, Context $context): object
    {
        $accessor = $context->accessors->get($this->metadata);

        foreach ($accessor->beforeDenormalization as $hook) {
            $value = $hook->beforeDenormalize($this->metadata, $value, $context);
        }

        $input = $value;
        $value = $context->coercers->array->coerce($input)
            ?? throw InvalidValueException::becauseInvalidType($this->metadata->name, $input)
                ->withTemplate($this->metadata->typeErrorMessage);

        $object = $accessor->instantiator->instantiate($value);

        foreach ($this->writable as $property) {
            $name = $property->metadata->name;
            $key = $property->metadata->externalName;

            if (!\array_key_exists($key, $value)) {
                $default = $property->metadata->default
                    ?? throw InvalidValueException::becauseFieldIsMissing($key, new PropertySegment($key))
                        ->withTemplate($property->metadata->undefinedErrorMessage);

                $accessor->writers[$name]->write($object, $default->value);

                continue;
            }

            $field = $value[$key];
            $isStrict = $property->metadata->write?->isStrict;

            try {
                $accessor->writers[$name]->write($object, $property->type->denormalize(
                    $field,
                    $isStrict === null ? $context : $context->withStrictTypes($isStrict),
                ));
            } catch (NotInterceptableExceptionInterface $e) {
                throw $e;
            } catch (\Exception $e) {
                throw InvalidValueException::wrapWithPath($e, new PropertySegment($key), $field)
                    ->withTemplate($property->metadata->typeErrorMessage);
            }
        }

        foreach ($accessor->afterDenormalization as $hook) {
            $hook->afterDenormalize($this->metadata, $object, $input, $context);
        }

        return $object;
    }

    /**
     * Checks the object as deep as {@see self::normalize()} does: each
     * property must be readable, such as initialized, and of its type.
     *
     * @throws TypeDefinitionException in case of a strategy rejects the
     *         class or a property
     */
    public function isNormalizable(mixed $value, Context $context): bool
    {
        if (!$value instanceof $this->metadata->name) {
            return false;
        }

        $accessor = $context->accessors->get($this->metadata);

        foreach ($accessor->beforeNormalization as $hook) {
            $value = $hook->beforeNormalize($this->metadata, $value, $context);
        }

        foreach ($this->readable as $property) {
            try {
                $field = $accessor->readers[$property->metadata->name]->read($value);
            } catch (\Error) {
                return false;
            }

            if ($property->metadata->skip !== [] && self::isSkipped($property, $value, $field)) {
                continue;
            }

            if (!$property->type->isNormalizable($field, $context)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the values of the properties by their keys in the data, as
     * changed by the hooks: an array or a {@see \stdClass} by the class or
     * by the configuration.
     *
     * @return array<array-key, mixed>|\stdClass
     * @throws InvalidValueException in case of the value is not an object of
     *         the class or one of its properties cannot be normalized
     * @throws TypeDefinitionException in case of a strategy rejects the
     *         class or a property
     */
    public function normalize(mixed $value, Context $context): array|\stdClass
    {
        if (!$value instanceof $this->metadata->name) {
            throw InvalidValueException::becauseInvalidType($this->metadata->name, $value)
                ->withTemplate($this->metadata->typeErrorMessage);
        }

        $accessor = $context->accessors->get($this->metadata);

        foreach ($accessor->beforeNormalization as $hook) {
            $value = $hook->beforeNormalize($this->metadata, $value, $context);
        }

        $result = [];

        foreach ($this->readable as $property) {
            $key = $property->metadata->externalName;

            try {
                $field = $accessor->readers[$property->metadata->name]->read($value);
            } catch (\Error $e) {
                throw InvalidValueException::wrapWithPath($e, new PropertySegment($key), null);
            }

            if ($property->metadata->skip !== [] && self::isSkipped($property, $value, $field)) {
                continue;
            }

            try {
                $result[$key] = $property->type->normalize($field, $context);
            } catch (NotInterceptableExceptionInterface $e) {
                throw $e;
            } catch (\Exception $e) {
                throw InvalidValueException::wrapWithPath($e, new PropertySegment($key), $field)
                    ->withTemplate($property->metadata->typeErrorMessage);
            }
        }

        foreach ($accessor->afterNormalization as $hook) {
            $result = $hook->afterNormalize($this->metadata, $value, $result, $context);
        }

        if ($this->metadata->isNormalizeAsArray ?? $context->config->isObjectAsArray) {
            return $result;
        }

        return (object) $result;
    }

    /**
     * Returns {@see true} in case of a skip condition of the property is
     * matched by the read value.
     *
     * @param Property<mixed> $property
     */
    private static function isSkipped(Property $property, object $object, mixed $field): bool
    {
        foreach ($property->metadata->skip as $condition) {
            if ($condition->isMatched($object, $field)) {
                return true;
            }
        }

        return false;
    }
}
