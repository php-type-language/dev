<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser\Grammar\Rule;

use TypeLang\PhpDoc\Exception\ParsingExceptionInterface;
use TypeLang\PhpDoc\Parser\Grammar\Context;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;

/**
 * A rule that declares part of a tag's shape.
 *
 * Casting a rule to a string yields its readable form, e.g.
 * `<URI> [ <description> ]`, used in error messages.
 */
abstract class Rule implements \Stringable
{
    /**
     * Matches the rule against the input, recording its captures.
     *
     * @throws NoMatchException when the rule does not apply
     * @throws ParsingExceptionInterface when the input is malformed
     */
    abstract public function match(Context $context): void;

    abstract public function __toString(): string;
}
