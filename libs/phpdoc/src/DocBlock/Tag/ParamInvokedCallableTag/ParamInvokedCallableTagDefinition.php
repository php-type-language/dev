<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\ParamInvokedCallableTag;

use TypeLang\PhpDoc\DocBlock\Combinator\DescriptionCombinator;
use TypeLang\PhpDoc\DocBlock\Combinator\VariableCombinator;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;
use TypeLang\PhpDoc\DocBlock\Tag\Definition\TagPayload;
use TypeLang\PhpDoc\DocBlock\Tag\Definition\Spec;
use TypeLang\PhpDoc\DocBlock\Tag\TagDefinition;

/**
 * A callable argument annotated by the moment at which it is invoked.
 */
abstract class ParamInvokedCallableTagDefinition extends TagDefinition
{
    /**
     * @param non-empty-string $name canonical name of the concrete tag
     */
    public function __construct(string $name)
    {
        parent::__construct(
            name: $name,
            spec: Spec::sequence(
                Spec::rule(VariableCombinator::NAME, 'variable'),
                Spec::maybe(
                    Spec::rule(DescriptionCombinator::NAME, 'description'),
                ),
            ),
            isInline: false,
        );
    }

    final public function create(string $name, TagPayload $result): ParamInvokedCallableTag
    {
        /** @var non-empty-string $variable */
        $variable = $result->get('variable');

        /** @var DescriptionInterface|null $description */
        $description = $result->find('description');

        return $this->make($variable, $description);
    }

    /**
     * @param non-empty-string $variable
     */
    abstract protected function make(
        string $variable,
        ?DescriptionInterface $description,
    ): ParamInvokedCallableTag;
}
