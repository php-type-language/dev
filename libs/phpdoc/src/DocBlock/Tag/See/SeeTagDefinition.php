<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\See;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Grammar\ReferenceGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\UriGrammarRule;
use TypeLang\PhpDoc\DocBlock\Reference\CodeReference;
use TypeLang\PhpDoc\DocBlock\Reference\UriReference;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;
use TypeLang\PhpDoc\Parser\Grammar\MatchedResult;
use TypeLang\PhpDoc\Parser\Grammar\Rule\Description;
use TypeLang\PhpDoc\Parser\Grammar\Rule\MatchRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\OneOf;
use TypeLang\PhpDoc\Parser\Grammar\Rule\Optional;
use TypeLang\PhpDoc\Parser\Grammar\Rule\Rule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\SequenceOf;

/**
 * The "`@see`" tag can be used to define a {@see ElementReference element} or
 * to an {@see UriReference external URI}.
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
 * "@see" ( <reference> | <URI> ) [ <description> ]
 * ```
 *
 * @link https://www.ietf.org/rfc/rfc2396.txt RFC2396
 */
final class SeeTagDefinition extends TagDefinition
{
    public const string NAME = 'see';

    public private(set) string $name = self::NAME;

    public readonly Rule $rule;

    public function __construct()
    {
        $this->rule = new SequenceOf(
            new OneOf(
                new MatchRule(ReferenceGrammarRule::NAME, 'ref'),
                new MatchRule(UriGrammarRule::NAME, 'ref'),
            ),
            new Optional(
                new Description('description'),
            ),
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
