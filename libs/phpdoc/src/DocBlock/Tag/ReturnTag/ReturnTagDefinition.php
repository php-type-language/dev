<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\ReturnTag;

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
 * The "`@return`" tag documents the value that a function or method returns to
 * its caller.
 *
 * The "`@return`" tag MAY be followed by a description that clarifies the
 * meaning of the returned value.
 *
 * ```
 * "@return" <Type> [ <Description> ]
 * ```
 */
final class ReturnTagDefinition extends TagDefinition
{
    public const string NAME = 'return';

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

    public function create(string $name, MatchedResult $result): ReturnTag
    {
        /** @var TypeReference $type */
        $type = $result->get('type');

        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new ReturnTag(self::NAME, $type, $description);
    }
}
