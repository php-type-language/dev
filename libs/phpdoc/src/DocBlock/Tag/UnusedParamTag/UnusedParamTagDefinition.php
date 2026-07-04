<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\UnusedParamTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Grammar\DescriptionGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\VariableGrammarRule;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;
use TypeLang\PhpDoc\Parser\Grammar\MatchedResult;
use TypeLang\PhpDoc\Parser\Grammar\Rule\MatchRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\Optional;
use TypeLang\PhpDoc\Parser\Grammar\Rule\SequenceOf;

/**
 * The "`@unused-param`" tag marks an argument that is intentionally left
 * unused.
 *
 * ```
 * "@unused-param" <Variable> [ <Description> ]
 * ```
 */
final class UnusedParamTagDefinition extends TagDefinition
{
    public const string NAME = 'unused-param';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            rule: new SequenceOf(
                new MatchRule(VariableGrammarRule::NAME, 'variable'),
                new Optional(
                    new MatchRule(DescriptionGrammarRule::NAME, 'description'),
                ),
            ),
            isInline: false,
        );
    }

    public function create(string $name, MatchedResult $result): UnusedParamTag
    {
        /** @var non-empty-string $variable */
        $variable = $result->get('variable');

        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new UnusedParamTag(self::NAME, $variable, $description);
    }
}
