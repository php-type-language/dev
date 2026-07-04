<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Grammar\DescriptionGrammarRule;
use TypeLang\PhpDoc\Parser\Grammar\MatchedResult;
use TypeLang\PhpDoc\Parser\Grammar\Rule\MatchRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\Optional;
use TypeLang\PhpDoc\Parser\Grammar\Rule\Rule;

final class GenericTagDefinition extends TagDefinition
{
    public const string NAME = '<any>';

    public private(set) string $name = self::NAME;

    public readonly Rule $rule;

    public function __construct()
    {
        $this->rule = new Optional(
            new MatchRule(DescriptionGrammarRule::NAME, 'description'),
        );
    }

    public function create(string $name, MatchedResult $result): GenericTag
    {
        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return new GenericTag(
            name: $name,
            description: $description,
        );
    }
}
