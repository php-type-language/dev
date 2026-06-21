<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\Standard;

use TypeLang\Parser\TypeParser as TypesParser;
use TypeLang\Parser\TypeParserInterface as TypesParserInterface;
use TypeLang\PHPDoc\Parser\Description\DescriptionParserInterface;
use TypeLang\PHPDoc\Tag\Content;
use TypeLang\PHPDoc\Tag\Factory\FactoryInterface;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Literal\VariableLiteralNode;

/**
 * This class is responsible for creating "`@param`" tags.
 *
 * See {@see ParamTag} for details about this tag.
 */
final class ParamTagFactory implements FactoryInterface
{
    public function __construct(
        private readonly TypesParserInterface $parser = new TypesParser(tolerant: true),
    ) {}

    private function isVariable(string $content): bool
    {
        return \str_starts_with($content, '&$')
            || \str_starts_with($content, '...$')
            || \str_starts_with($content, '&...$')
            || \str_starts_with($content, '$');
    }

    public function create(string $name, Content $content, DescriptionParserInterface $descriptions): ParamTag
    {
        $type = null;
        $output = $variadic = false;

        if (!$this->isVariable($content->value)) {
            $type = $content->nextType($name, $this->parser);
        }

        if (\str_starts_with($content->value, '&')) {
            $content->shift(1);
            $output = true;
        }

        if (\str_starts_with($content->value, '...')) {
            $content->shift(3);
            $variadic = true;
        }

        $variable = $content->nextVariable($name);

        return new ParamTag(
            name: $name,
            param: new CallableParameterNode(
                type: $type,
                name: new VariableLiteralNode('$' . $variable),
                output: $output,
                variadic: $variadic,
            ),
            description: \trim($content->value) !== ''
                ? $descriptions->parse(\rtrim($content->value))
                : null,
        );
    }
}
