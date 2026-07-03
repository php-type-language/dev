<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Tag\Rule;

use Boson\Component\Uri\Factory\UriFactory;
use Boson\Contracts\Uri\Factory\UriFactoryInterface;
use Boson\Contracts\Uri\UriInterface;
use TypeLang\PhpDoc\Parser\Grammar\Cursor;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;
use TypeLang\PhpDoc\Parser\Grammar\RuleInterface;

final readonly class UriGrammarRule implements RuleInterface
{
    public const string NAME = 'URI';

    public function __construct(
        private UriFactoryInterface $factory = new UriFactory(),
    ) {}

    public function __invoke(Cursor $cursor): UriInterface
    {
        $uri = $cursor->readWord();

        if ($uri === '') {
            throw new NoMatchException('Expected a URI');
        }

        return $this->factory->createUriFromString($uri);
    }
}
