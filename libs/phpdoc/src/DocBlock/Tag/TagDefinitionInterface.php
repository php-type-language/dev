<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag;

use TypeLang\PhpDoc\Parser\Grammar\MatchedResult;
use TypeLang\PhpDoc\Parser\Grammar\Rule\Rule;

/**
 * Declares a single tag.
 *
 * The shape of its body (a {@see Rule}) and how to build a {@see TagInterface}
 * from the parsed pieces.
 */
interface TagDefinitionInterface extends \Stringable
{
    /**
     * Canonical tag name
     *
     * @var non-empty-string
     */
    public string $name {
        get;
    }

    /**
     * The shape of the tag body.
     */
    public Rule $rule {
        get;
    }

    /**
     * Builds the tag from the values captured while matching {@see $rule}.
     *
     * @param non-empty-string $name the tag name, without the leading "@"
     */
    public function create(string $name, MatchedResult $result): TagInterface;
}
