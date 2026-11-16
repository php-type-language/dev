<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Reader\Internal;

use TypeLang\Mapper\Mapping\Exception\InvalidValueException;
use TypeLang\Mapper\Mapping\Metadata\DiscriminatorMetadata;
use TypeLang\Mapper\Mapping\Metadata\TypeMetadata;
use TypeLang\Parser\TypeParserInterface;
use TypeLang\Type\TypeNode;

/**
 * Creates the polymorphism of a class from the values of an attribute or
 * of a config: checks them and parses the types by the place.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Mapping\Reader
 */
final class DiscriminatorFactory
{
    /**
     * Returns {@see null} in case of a value is wrong, its error is added.
     *
     * @param TypeParserInterface<TypeNode, string> $parser
     */
    public static function create(
        mixed $field,
        mixed $map,
        mixed $otherwise,
        SourceContext $context,
        TypeParserInterface $parser,
        ErrorCollector $errors,
    ): ?DiscriminatorMetadata {
        if (!\is_string($field) || $field === '') {
            $errors->addError(InvalidValueException::becauseValueIsNotNonEmptyString(
                'field',
                $context->target,
                $context->source,
            ));

            return null;
        }

        if (!\is_array($map) || $map === []) {
            $errors->addError(InvalidValueException::becauseValueIsNotNonEmptyMap(
                'map',
                $context->target,
                $context->source,
            ));

            return null;
        }

        $types = [];

        foreach ($map as $tag => $type) {
            if (!\is_string($type) || $type === '') {
                $errors->addError(InvalidValueException::becauseValueIsNotNonEmptyString(
                    'map',
                    $context->target,
                    $context->source,
                ));

                return null;
            }

            $types[$tag] = $context->parseType($parser, $type);
        }

        return new DiscriminatorMetadata(
            field: $field,
            map: $types,
            source: $context->source,
            default: self::findDefault($otherwise, $context, $parser, $errors),
        );
    }

    /**
     * @param TypeParserInterface<TypeNode, string> $parser
     */
    private static function findDefault(
        mixed $otherwise,
        SourceContext $context,
        TypeParserInterface $parser,
        ErrorCollector $errors,
    ): ?TypeMetadata {
        if ($otherwise === null) {
            return null;
        }

        if (!\is_string($otherwise) || $otherwise === '') {
            $errors->addError(InvalidValueException::becauseValueIsNotNonEmptyString(
                'otherwise',
                $context->target,
                $context->source,
            ));

            return null;
        }

        return $context->parseType($parser, $otherwise);
    }
}
