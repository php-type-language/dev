<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\PropertyTag;

use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Grammar\DescriptionGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\TypeGrammarRule;
use TypeLang\PhpDoc\DocBlock\Grammar\VariableGrammarRule;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;
use TypeLang\PhpDoc\DocBlock\Type\TypeStatement;
use TypeLang\PhpDoc\Parser\Grammar\MatchedResult;
use TypeLang\PhpDoc\Parser\Grammar\Rule\MatchRule;
use TypeLang\PhpDoc\Parser\Grammar\Rule\Optional;
use TypeLang\PhpDoc\Parser\Grammar\Rule\SequenceOf;

/**
 * A magic property that a class exposes for reading, writing, or both.
 */
abstract class MagicPropertyTagDefinition extends TagDefinition
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
                new MatchRule(VariableGrammarRule::NAME, 'variable'),
                new Optional(
                    new MatchRule(DescriptionGrammarRule::NAME, 'description'),
                ),
            ),
            isInline: false,
        );
    }

    final public function create(string $name, MatchedResult $result): MagicPropertyTag
    {
        /** @var TypeStatement $type */
        $type = $result->get('type');

        /** @var non-empty-string $variable */
        $variable = $result->get('variable');

        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return $this->make($type, $variable, $description);
    }

    /**
     * @param non-empty-string $variable
     */
    abstract protected function make(
        TypeStatement $type,
        string $variable,
        ?DescriptionInterface $description,
    ): MagicPropertyTag;
}
