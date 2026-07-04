<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\DocBlock\Grammar;

use TypeLang\PhpDoc\DocBlock\Reference\ClassConstantReference;
use TypeLang\PhpDoc\DocBlock\Reference\ClassMethodReference;
use TypeLang\PhpDoc\DocBlock\Reference\ClassPropertyReference;
use TypeLang\PhpDoc\DocBlock\Reference\CodeReference;
use TypeLang\PhpDoc\DocBlock\Reference\FunctionReference;
use TypeLang\PhpDoc\DocBlock\Reference\SymbolReference;
use TypeLang\PhpDoc\DocBlock\Reference\VariableReference;
use TypeLang\PhpDoc\Parser\Grammar\Cursor;
use TypeLang\PhpDoc\Parser\Grammar\Exception\NoMatchException;
use TypeLang\PhpDoc\Parser\Grammar\RuleInterface;

/**
 * Reads a reference to a code element: a class, a function, a class method, a
 * class constant, a class property or a variable.
 */
final readonly class ReferenceGrammarRule implements RuleInterface
{
    public const string NAME = 'reference';

    /**
     * The ASCII characters allowed in a method, constant, property or variable
     * name.
     */
    private const string NAME_CHARS = 'abcdefghijklmnopqrstuvwxyz'
        . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
        . '0123456789'
        . '_';

    private string $nameTerminators;
    private string $symbolTerminators;

    public function __construct()
    {
        $this->nameTerminators = self::terminatorsExcept(self::NAME_CHARS);
        $this->symbolTerminators = self::terminatorsExcept(self::NAME_CHARS . '\\');
    }

    public function __invoke(Cursor $cursor): CodeReference
    {
        $reference = $cursor->readWord();

        if ($reference === '') {
            throw new NoMatchException('Expected a code reference');
        }

        return $this->parse($reference)
            ?? throw new NoMatchException(\sprintf('Invalid code reference "%s"', $reference));
    }

    private function parse(string $reference): ?CodeReference
    {
        // A variable: "$name".
        if ($reference[0] === '$') {
            $name = $this->name(\substr($reference, 1));

            return $name !== null ? new VariableReference($name) : null;
        }

        // A class member: "Class::member".
        $separator = \strpos($reference, '::');

        if ($separator !== false) {
            return $this->parseClassMember(
                \substr($reference, 0, $separator),
                \substr($reference, $separator + 2),
            );
        }

        // A function: "name()".
        if (\str_ends_with($reference, '()')) {
            $symbol = $this->symbol(\substr($reference, 0, -2));

            return $symbol !== null ? new FunctionReference($symbol) : null;
        }

        // A class: "name".
        $symbol = $this->symbol($reference);

        return $symbol !== null ? new SymbolReference($symbol) : null;
    }

    private function parseClassMember(string $class, string $member): ?CodeReference
    {
        $symbol = $this->symbol($class);

        if ($symbol === null || $member === '') {
            return null;
        }

        // A property: "$name".
        if ($member[0] === '$') {
            $name = $this->name(\substr($member, 1));

            return $name !== null ? new ClassPropertyReference($symbol, $name) : null;
        }

        // A method: "name()".
        if (\str_ends_with($member, '()')) {
            $name = $this->name(\substr($member, 0, -2));

            return $name !== null ? new ClassMethodReference($symbol, $name) : null;
        }

        // A constant: "name".
        $name = $this->name($member);

        return $name !== null ? new ClassConstantReference($symbol, $name) : null;
    }

    /**
     * Returns $value when it is a class name (letters, digits, "_" and "\"),
     * otherwise null.
     *
     * @return non-empty-string|null
     */
    private function symbol(string $value): ?string
    {
        return $value !== '' && \strcspn($value, $this->symbolTerminators) === \strlen($value)
            ? $value
            : null;
    }

    /**
     * Returns $value when it is a member name (letters, digits and "_"),
     * otherwise null.
     *
     * @return non-empty-string|null
     */
    private function name(string $value): ?string
    {
        return $value !== '' && \strcspn($value, $this->nameTerminators) === \strlen($value)
            ? $value
            : null;
    }

    /**
     * The ASCII characters that are not in $allowed; a value built only from
     * allowed characters (any byte above 0x7F included) contains none of them.
     */
    private static function terminatorsExcept(string $allowed): string
    {
        $mask = '';

        for ($byte = 0x00; $byte <= 0x7F; ++$byte) {
            $char = \chr($byte);

            if (!\str_contains($allowed, $char)) {
                $mask .= $char;
            }
        }

        return $mask;
    }
}
