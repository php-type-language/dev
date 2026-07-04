<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\RequireInheritanceTag;

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
 * A tag that constrains a trait so that it may only be used within a class
 * related to a given type.
 */
abstract class RequireInheritanceTagDefinition extends TagDefinition
{
    /**
     * @param non-empty-string $name canonical name of the concrete tag
     */
    public function __construct(string $name)
    {
        parent::__construct(
            name: $name,
            rule: new SequenceOf(
                new MatchRule(TypeGrammarRule::NAME, 'type'),
                new Optional(
                    new MatchRule(DescriptionGrammarRule::NAME, 'description'),
                ),
            ),
            isInline: false,
        );
    }

    final public function create(string $name, MatchedResult $result): RequireInheritanceTag
    {
        /** @var TypeStatement $type */
        $type = $result->get('type');

        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return $this->make($type, $description);
    }

    abstract protected function make(
        TypeStatement $type,
        ?DescriptionInterface $description,
    ): RequireInheritanceTag;
}
