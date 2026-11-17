<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Type;

use TypeLang\Mapper\Context\Context;
use TypeLang\Mapper\Diagnostics\Path\Segment\KeySegment;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Exception\NotInterceptableExceptionInterface;
use TypeLang\Mapper\Exception\TypeDefinitionException;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\NamespaceName;

/**
 * `mixed`: data is denormalized as is. On normalization an object is
 * normalized by the type of its class from {@see Context::$types}, an array
 * and a {@see \stdClass} element by element, other values are kept as is.
 *
 * @template-implements TypeInterface<mixed>
 */
final class MixedType implements TypeInterface
{
    public function isDenormalizable(mixed $value, Context $context): bool
    {
        return true;
    }

    public function denormalize(mixed $value, Context $context): mixed
    {
        return $value;
    }

    /**
     * @throws TypeDefinitionException in case of the type of an object
     *         cannot be built
     */
    public function isNormalizable(mixed $value, Context $context): bool
    {
        if (self::isDynamicObject($value)) {
            $value = \get_object_vars($value);
        }

        if (\is_array($value)) {
            foreach ($value as $item) {
                if (!$this->isNormalizable($item, $context)) {
                    return false;
                }
            }

            return true;
        }

        if (!\is_object($value)) {
            return $value === null || \is_scalar($value);
        }

        return $context->types->getType(self::createClassNode($value))->isNormalizable($value, $context);
    }

    /**
     * @throws InvalidValueException in case of an element or an object
     *         cannot be normalized
     * @throws TypeDefinitionException in case of the type of an object
     *         cannot be built
     */
    public function normalize(mixed $value, Context $context): mixed
    {
        if (self::isDynamicObject($value)) {
            $value = \get_object_vars($value);
        }

        if (\is_array($value)) {
            $result = [];

            foreach ($value as $key => $item) {
                try {
                    $result[$key] = $this->normalize($item, $context);
                } catch (NotInterceptableExceptionInterface $e) {
                    throw $e;
                } catch (\Exception $e) {
                    throw InvalidValueException::wrapWithPath($e, new KeySegment($key), $item);
                }
            }

            return $result;
        }

        if (\is_object($value)) {
            return $context->types->getType(self::createClassNode($value))->normalize($value, $context);
        }

        if ($value === null || \is_scalar($value)) {
            return $value;
        }

        throw InvalidValueException::becauseInvalidType('mixed', $value);
    }

    /**
     * Returns {@see true} for an object of exactly {@see \stdClass}.
     *
     * @phpstan-assert-if-true \stdClass $value
     */
    private static function isDynamicObject(mixed $value): bool
    {
        return \is_object($value) && $value::class === \stdClass::class;
    }

    /**
     * Returns the node of the class of the object, with a fully qualified
     * name.
     */
    private static function createClassNode(object $value): NamedTypeNode
    {
        $words = \explode('\\', $value::class);

        /** @var non-empty-string */
        $last = \array_pop($words);

        if ($words === []) {
            return new NamedTypeNode(new Name($last));
        }

        return new NamedTypeNode(new Name(
            $last,
            new NamespaceName(\implode('\\', $words)),
        ));
    }
}
