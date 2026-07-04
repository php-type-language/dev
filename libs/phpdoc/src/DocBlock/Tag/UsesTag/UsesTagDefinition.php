<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\UsesTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Grammar\DescriptionGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\ReferenceGrammarRule;
use TypeLang\PhpDoc\DocBlock\Reference\CodeReference;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;
use TypeLang\PhpDoc\Parser\Grammar\MatchedResult;
use TypeLang\PhpDoc\Parser\Grammar\Rule\MatchRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\OptionalityRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\SequencingRule;

/**
 * The "`@uses`" tag indicates that the described element uses the referenced
 * one.
 *
 * ```
 * "@uses" <Reference> [ <Description> ]
 * ```
 */
final class UsesTagDefinition extends TagDefinition
{
    public const string NAME = 'uses';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            rule: new SequencingRule(
                new MatchRule(ReferenceGrammarRule::NAME, 'reference'),
                new OptionalityRule(
                    new MatchRule(DescriptionGrammarRule::NAME, 'description'),
                ),
            ),
            isInline: false,
        );
    }

    public function create(string $name, MatchedResult $result): UsesTag
    {
        /** @var CodeReference $reference */
        $reference = $result->get('reference');

        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new UsesTag(self::NAME, $reference, $description);
    }
}
