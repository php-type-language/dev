<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\MixinTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Grammar\DescriptionGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\TypeGrammarRule;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;
use TypeLang\PhpDoc\DocBlock\Type\TypeStatement;
use TypeLang\PhpDoc\Parser\Grammar\MatchedResult;
use TypeLang\PhpDoc\Parser\Grammar\Rule\MatchRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\Optional;
use TypeLang\PhpDoc\Parser\Grammar\Rule\SequenceOf;

/**
 * The "`@mixin`" tag declares that the members of the referenced type are
 * magically available on the described class.
 *
 * ```
 * "@mixin" <Type> [ <Description> ]
 * ```
 */
final class MixinTagDefinition extends TagDefinition
{
    public const string NAME = 'mixin';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            rule: new SequenceOf(
                new MatchRule(TypeGrammarRule::NAME, 'type'),
                new Optional(
                    new MatchRule(DescriptionGrammarRule::NAME, 'description'),
                ),
            ),
            isInline: false,
        );
    }

    public function create(string $name, MatchedResult $result): MixinTag
    {
        /** @var TypeStatement $type */
        $type = $result->get('type');

        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new MixinTag(self::NAME, $type, $description);
    }
}
