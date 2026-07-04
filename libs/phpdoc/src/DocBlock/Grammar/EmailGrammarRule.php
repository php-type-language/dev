<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Grammar;

use TypeLang\PhpDoc\Parser\Grammar\Cursor;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;
use TypeLang\PhpDoc\Parser\Grammar\RuleInterface;

/**
 * Reads an email address up to its closing ">".
 *
 * @implements RuleInterface<non-empty-string>
 */
final readonly class EmailGrammarRule implements RuleInterface
{
    public const string NAME = 'Email';

    /**
     * @return non-empty-string
     */
    public function __invoke(Cursor $cursor): string
    {
        $email = \trim($cursor->readUntil('>'));

        if ($email === '') {
            throw new NoMatchException('Expected an email address');
        }

        return $email;
    }
}
