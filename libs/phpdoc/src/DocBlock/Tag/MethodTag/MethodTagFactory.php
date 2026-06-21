<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\MethodTag;

use TypeLang\Parser\TypeParser as TypesParser;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Factory\TagFactoryInterface;
use TypeLang\PhpDoc\Exception\InvalidTagException;
use TypeLang\PhpDoc\Parser\Content\OptionalTypeReader;
use TypeLang\PhpDoc\Parser\Content\OptionalValueReader;
use TypeLang\PhpDoc\Parser\Content\Stream;
use TypeLang\PhpDoc\Parser\Content\TypeReader;
use TypeLang\PhpDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\Type\CallableTypeNode;
use TypeLang\Type\NamedTypeNode;

/**
 * This class is responsible for creating "`@method`" tags.
 *
 * See {@see MethodTag} for details about this tag.
 */
final class MethodTagFactory implements TagFactoryInterface
{
    public function __construct(
        private readonly TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {}

    public function create(string $tag, string $content, DescriptionParserInterface $descriptions): MethodTag
    {
        $stream = new Stream($tag, $content);

        $isStatic = $stream->apply(new OptionalValueReader('static')) !== null;

        try {
            $type = $stream->apply(new TypeReader($this->parser));
        } catch (InvalidTagException $e) {
            $type = $isStatic ? new NamedTypeNode('static') : throw $e;
        }

        $callable = null;

        if (!$type instanceof CallableTypeNode) {
            $callable = $stream->apply(new OptionalTypeReader($this->parser));
        }

        // In case of return type has not been defined then we swap first
        // defined type as method signature definition.
        if ($callable === null) {
            $callable = $type;
            $type = null;
        }

        if (!$callable instanceof CallableTypeNode) {
            throw $stream->toException(\sprintf(
                'Tag @%s must contain the method signature',
                $tag,
            ));
        }

        if ($callable->type !== null && $type !== null) {
            throw $stream->toException(\sprintf(
                'You can specify the return type of the @%s tag before or '
                    . 'after the method`s signature, but not both',
                $tag,
            ));
        }

        if (!$callable->name->isSimple()) {
            throw $stream->toException(\sprintf(
                'Tag @%s must contain the method name, but FQN "%s" given',
                $tag,
                $callable->name->toString(),
            ));
        }

        return new MethodTag(
            name: $tag,
            method: $callable->name->toString(),
            type: $callable->type ?? $type,
            parameters: $callable->parameters,
            isStatic: $isStatic,
            description: $stream->toOptionalDescription($descriptions),
        );
    }
}
