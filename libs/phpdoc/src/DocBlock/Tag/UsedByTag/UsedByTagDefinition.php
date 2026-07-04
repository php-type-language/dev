<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\UsedByTag;

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
 * The "`@used-by`" tag indicates that the described element is used by the
 * referenced one.
 *
 * ```
 * "@used-by" <Reference> [ <Description> ]
 * ```
 */
final class UsedByTagDefinition extends TagDefinition
{
    public const string NAME = 'used-by';

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

    public function create(string $name, MatchedResult $result): UsedByTag
    {
        /** @var CodeReference $reference */
        $reference = $result->get('reference');

        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new UsedByTag(self::NAME, $reference, $description);
    }
}
