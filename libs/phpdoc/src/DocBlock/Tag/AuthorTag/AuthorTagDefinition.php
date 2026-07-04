<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\AuthorTag;

use TypeLang\PhpDoc\DocBlock\Grammar\AuthorNameGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\EmailGrammarRule;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;
use TypeLang\PhpDoc\Parser\Grammar\MatchedResult;
use TypeLang\PhpDoc\Parser\Grammar\Rule\LiteralRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\MatchRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\OptionalityRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\SequencingRule;

/**
 * The "`@author`" tag documents the author of an element, together with an
 * optional email address.
 *
 * ```
 * "@author" <AuthorName> [ "<" <Email> ">" ]
 * ```
 */
final class AuthorTagDefinition extends TagDefinition
{
    public const string NAME = 'author';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            rule: new SequencingRule(
                new MatchRule(AuthorNameGrammarRule::NAME, 'author'),
                new OptionalityRule(
                    new SequencingRule(
                        new LiteralRule('<'),
                        new MatchRule(EmailGrammarRule::NAME, 'email'),
                        new LiteralRule('>'),
                    ),
                ),
            ),
            isInline: false,
        );
    }

    public function create(string $name, MatchedResult $result): AuthorTag
    {
        /** @var non-empty-string $author */
        $author = $result->get('author');

        /** @var non-empty-string|null $email */
        $email = $result->find('email');

        return new AuthorTag(self::NAME, $author, $email);
    }
}
