<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\ParamOutTag;

use TypeLang\PhpDoc\DocBlock\Combinator\DescriptionCombinator;
use TypeLang\PhpDoc\DocBlock\Combinator\TypeCombinator;
use TypeLang\PhpDoc\DocBlock\Combinator\VariableCombinator;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Reference\TypeReference;
use TypeLang\PhpDoc\DocBlock\Tag\Definition\TagPayload;
use TypeLang\PhpDoc\DocBlock\Tag\Definition\Spec;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;

/**
 * The "`@param-out`" tag documents the type that a by-reference argument holds
 * after the function or method returns.
 *
 * ```
 * "@param-out" <Type> <Variable> [ <Description> ]
 * ```
 */
final class ParamOutTagDefinition extends TagDefinition
{
    public const string NAME = 'param-out';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            spec: Spec::sequence(
                Spec::rule(TypeCombinator::NAME, 'type'),
                Spec::rule(VariableCombinator::NAME, 'variable'),
                Spec::maybe(
                    Spec::rule(DescriptionCombinator::NAME, 'description'),
                ),
            ),
            isInline: false,
        );
    }

    public function create(string $name, TagPayload $result): ParamOutTag
    {
        /** @var TypeReference $type */
        $type = $result->get('type');

        /** @var non-empty-string $variable */
        $variable = $result->get('variable');

        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new ParamOutTag(self::NAME, $type, $variable, $description);
    }
}
