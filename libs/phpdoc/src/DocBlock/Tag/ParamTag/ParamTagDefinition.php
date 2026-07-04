<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\ParamTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Grammar\DescriptionGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\TypeGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\VariableGrammarRule;
use TypeLang\PhpDoc\DocBlock\Reference\TypeReference;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;
use TypeLang\PhpDoc\Parser\Grammar\MatchedResult;
use TypeLang\PhpDoc\Parser\Grammar\Rule\MatchRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\OptionalityRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\SequencingRule;

/**
 * The "`@param`" tag documents a single argument of a function or method.
 *
 * The "`@param`" tag MAY be followed by a description that clarifies the
 * meaning of the argument.
 *
 * ```
 * "@param" <Type> <Variable> [ <Description> ]
 * ```
 */
final class ParamTagDefinition extends TagDefinition
{
    public const string NAME = 'param';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            rule: new SequencingRule(
                new MatchRule(TypeGrammarRule::NAME, 'type'),
                new MatchRule(VariableGrammarRule::NAME, 'variable'),
                new OptionalityRule(
                    new MatchRule(DescriptionGrammarRule::NAME, 'description'),
                ),
            ),
            isInline: false,
        );
    }

    public function create(string $name, MatchedResult $result): ParamTag
    {
        /** @var TypeReference $type */
        $type = $result->get('type');

        /** @var non-empty-string $variable */
        $variable = $result->get('variable');

        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new ParamTag(self::NAME, $type, $variable, $description);
    }
}
