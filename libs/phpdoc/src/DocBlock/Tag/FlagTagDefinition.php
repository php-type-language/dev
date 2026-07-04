<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Grammar\DescriptionGrammarRule;
use TypeLang\PhpDoc\Parser\Grammar\MatchedResult;
use TypeLang\PhpDoc\Parser\Grammar\Rule\MatchRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\OptionalityRule;

/**
 * A tag that marks an element and carries no value beyond an optional
 * description.
 */
abstract class FlagTagDefinition extends TagDefinition
{
    /**
     * @param non-empty-string $name canonical name of the concrete tag
     */
    public function __construct(string $name, bool $isInline = false)
    {
        parent::__construct(
            name: $name,
            rule: new OptionalityRule(
                new MatchRule(DescriptionGrammarRule::NAME, 'description'),
            ),
            isInline: $isInline,
        );
    }

    final public function create(string $name, MatchedResult $result): FlagTag
    {
        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return $this->make($description);
    }

    abstract protected function make(?DescriptionInterface $description): FlagTag;
}
