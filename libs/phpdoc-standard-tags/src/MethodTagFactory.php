<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Standard;

use TypeLang\Parser\Node\Stmt\CallableTypeNode;
use TypeLang\Parser\Parser as TypesParser;
use TypeLang\Parser\ParserInterface as TypesParserInterface;
use TypeLang\PHPDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PHPDoc\Tag\Content;
use TypeLang\PHPDoc\Tag\Factory\FactoryInterface;

/**
 * This class is responsible for creating "`@method`" tags.
 *
 * See {@see MethodTag} for details about this tag.
 */
final class MethodTagFactory implements FactoryInterface
{
    public function __construct(
        private readonly TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {}

    public function create(string $name, Content $content, DescriptionParserInterface $descriptions): MethodTag
    {
        $isStatic = $content->nextOptionalValue('static') !== null;
        $returnType = $content->nextType($name, $this->parser);
        $callableType = $content->nextOptionalType($this->parser);

        // In case of return type has not been defined then we swap first
        // defined type as method signature definition.
        if ($callableType === null) {
            $callableType = $returnType;
            $returnType = null;
        }

        if (!$callableType instanceof CallableTypeNode) {
            throw $content->getTagException(
                message: \sprintf(
                    'The @%s annotation must contain the method signature',
                    $name,
                ),
            );
        }

        if ($callableType->type !== null && $returnType !== null) {
            throw $content->getTagException(
                message: \sprintf('You can specify the return type of '
                    . 'a method of the @%s annotation before or after the '
                    . 'method`s signature, but not both', $name),
            );
        }

        $callableType->type ??= $returnType;

        return new MethodTag(
            name: $name,
            type: $callableType,
            static: $isStatic,
            description: \trim($content->value) !== ''
                ? $descriptions->parse(\rtrim($content->value))
                : null,
        );
    }
}
