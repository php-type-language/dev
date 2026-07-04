<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Grammar;

use TypeLang\PhpDoc\Parser\Grammar\Cursor;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;
use TypeLang\PhpDoc\Parser\Grammar\RuleInterface;

/**
 * Reads a variable ("$name") and returns its name without the leading "$".
 *
 * @implements RuleInterface<non-empty-string>
 */
final readonly class VariableGrammarRule implements RuleInterface
{
    public const string NAME = 'Variable';

    /**
     * Validates a variable name: letters, digits and "_".
     */
    private NameValidator $names;

    public function __construct()
    {
        $this->names = new NameValidator();
    }

    /**
     * @return non-empty-string
     */
    public function __invoke(Cursor $cursor): string
    {
        $variable = $cursor->readWord();

        if ($variable === '' || $variable[0] !== '$') {
            throw new NoMatchException('Expected a variable');
        }

        return $this->names->validate(\substr($variable, 1))
            ?? throw new NoMatchException(\sprintf('Invalid variable "%s"', $variable));
    }
}
