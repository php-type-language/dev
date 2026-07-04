<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\SeeTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Grammar\DescriptionGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\ReferenceGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\UriGrammarRule;
use TypeLang\PhpDoc\DocBlock\Reference\CodeReference;
use TypeLang\PhpDoc\DocBlock\Reference\UriReference;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;
use TypeLang\PhpDoc\Parser\Grammar\MatchedResult;
use TypeLang\PhpDoc\Parser\Grammar\Rule\MatchRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\AlternationRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\OptionalityRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\SequencingRule;

/**
 * The "`@see`" tag can be used to define a {@see CodeReference element}.
 *
 * When defining a reference to other elements, you can refer to a specific
 * element by appending a double colon and providing the name of that element
 * (also called the 'Fully Qualified Name' or _FQN_).
 *
 * A URI MUST be complete and well-formed as specified in RFC 2396.
 *
 * The "`@see"` tag SHOULD have a description to provide additional information
 * regarding the relationship between the element and its target.
 *
 * The "`@see`" tag cannot refer to a namespace element.
 *
 * ```
 * "@see" ( <Reference> | <URI> ) [ <Description> ]
 * ```
 *
 * @link https://www.ietf.org/rfc/rfc2396.txt RFC2396
 */
final class SeeTagDefinition extends TagDefinition
{
    public const string NAME = 'see';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            rule: new SequencingRule(
                new AlternationRule(
                    new MatchRule(ReferenceGrammarRule::NAME, 'ref'),
                    new MatchRule(UriGrammarRule::NAME, 'ref'),
                ),
                new OptionalityRule(
                    new MatchRule(DescriptionGrammarRule::NAME, 'description'),
                ),
            ),
            isInline: true,
        );
    }

    public function create(string $name, MatchedResult $result): SeeTag
    {
        /** @var CodeReference|UriReference $reference */
        $reference = $result->get('ref');

        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new SeeTag(
            name: $name,
            reference: $reference,
            description: $description,
        );
    }
}
