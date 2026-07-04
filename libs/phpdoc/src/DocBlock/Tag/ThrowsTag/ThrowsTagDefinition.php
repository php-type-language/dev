<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\ThrowsTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Grammar\DescriptionGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\TypeGrammarRule;
use TypeLang\PhpDoc\DocBlock\Reference\TypeReference;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;
use TypeLang\PhpDoc\Parser\Grammar\MatchedResult;
use TypeLang\PhpDoc\Parser\Grammar\Rule\MatchRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\OptionalityRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\SequencingRule;

/**
 * The "`@throws`" tag indicates that a function or method is able to throw
 * a specific type of "\Throwable" (an exception or an error).
 *
 * The "`@throws`" tag MAY be followed by a description explaining when and why
 * the "\Throwable" is thrown.
 *
 * ```
 * "@throws" <Type> [ <Description> ]
 * ```
 */
final class ThrowsTagDefinition extends TagDefinition
{
    public const string NAME = 'throws';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            rule: new SequencingRule(
                new MatchRule(TypeGrammarRule::NAME, 'type'),
                new OptionalityRule(
                    new MatchRule(DescriptionGrammarRule::NAME, 'description'),
                ),
            ),
            isInline: false,
        );
    }

    public function create(string $name, MatchedResult $result): ThrowsTag
    {
        /** @var TypeReference $type */
        $type = $result->get('type');

        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new ThrowsTag(self::NAME, $type, $description);
    }
}
