<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Grammar;

use TypeLang\PhpDoc\Parser\Grammar\Cursor;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;
use TypeLang\PhpDoc\Parser\Grammar\RuleInterface;

/**
 * Reads an author name, that is everything up to an optional "<email>".
 *
 * @implements RuleInterface<non-empty-string>
 */
final readonly class AuthorNameGrammarRule implements RuleInterface
{
    public const string NAME = 'AuthorName';

    /**
     * @return non-empty-string
     */
    public function __invoke(Cursor $cursor): string
    {
        $name = \rtrim($cursor->readUntil('<'));

        if ($name === '') {
            throw new NoMatchException('Expected an author name');
        }

        return $name;
    }
}
