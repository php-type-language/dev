<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Grammar;

use TypeLang\PhpDoc\DocBlock\Reference\UriReference;
use TypeLang\PhpDoc\Parser\Grammar\Cursor;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;
use TypeLang\PhpDoc\Parser\Grammar\RuleInterface;

final readonly class UriGrammarRule implements RuleInterface
{
    public const string NAME = 'URI';

    public function __invoke(Cursor $cursor): UriReference
    {
        $uri = $cursor->readWord();

        if ($uri === '') {
            throw new NoMatchException('Expected a URI');
        }

        return new UriReference($uri);
    }
}
