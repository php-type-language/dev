<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Combinator;

use TypeLang\PhpDoc\DocBlock\Reference\UriReference;
use TypeLang\PhpDoc\Parser\Grammar\CombinatorInterface;
use TypeLang\PhpDoc\Parser\Grammar\Cursor;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;

final readonly class UriCombinator implements CombinatorInterface
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
