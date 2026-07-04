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
     * Validates a member name: letters, digits and "_".
     */
    private NameValidator $names;

    /**
     * Validates a class name: letters, digits, "_" and "\".
     */
    private NameValidator $symbols;

    public function __construct()
    {
        $this->names = new NameValidator();
        $this->symbols = new NameValidator('\\');
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
            return $this->parseVariable($reference);
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
            return $this->parseFunction($reference);
        }

        // A class or global constant: "name".
        return $this->parseSymbol($reference);
    }

    /**
     * Parses a variable: "$name".
     */
    private function parseVariable(string $reference): ?VariableReference
    {
        $name = $this->names->validate(\substr($reference, 1));

        return $name === null ? null : new VariableReference($name);
    }

    /**
     * Parses a function: "name()".
     */
    private function parseFunction(string $reference): ?FunctionReference
    {
        $symbol = $this->symbols->validate(\substr($reference, 0, -2));

        return $symbol === null ? null : new FunctionReference($symbol);
    }

    /**
     * Parses a class or global constant: "name".
     */
    private function parseSymbol(string $reference): ?SymbolReference
    {
        $symbol = $this->symbols->validate($reference);

        return $symbol === null ? null : new SymbolReference($symbol);
    }

    private function parseClassMember(string $class, string $member): ?CodeReference
    {
        $symbol = $this->symbols->validate($class);

        if ($symbol === null || $member === '') {
            return null;
        }

        // A property: "$name".
        if ($member[0] === '$') {
            return $this->parseClassProperty($symbol, $member);
        }

        // A method: "name()".
        if (\str_ends_with($member, '()')) {
            return $this->parseClassMethod($symbol, $member);
        }

        // A constant: "name".
        return $this->parseClassConstant($symbol, $member);
    }

    /**
     * Parses the "$name" part of a "Class::$name" property reference.
     *
     * @param non-empty-string $class
     */
    private function parseClassProperty(string $class, string $member): ?ClassPropertyReference
    {
        $name = $this->names->validate(\substr($member, 1));

        return $name === null ? null : new ClassPropertyReference($class, $name);
    }

    /**
     * Parses the "name()" part of a "Class::name()" method reference.
     *
     * @param non-empty-string $class
     */
    private function parseClassMethod(string $class, string $member): ?ClassMethodReference
    {
        $name = $this->names->validate(\substr($member, 0, -2));

        return $name === null ? null : new ClassMethodReference($class, $name);
    }

    /**
     * Parses the "name" part of a "Class::name" constant reference.
     *
     * @param non-empty-string $class
     */
    private function parseClassConstant(string $class, string $member): ?ClassConstantReference
    {
        $name = $this->names->validate($member);

        return $name === null ? null : new ClassConstantReference($class, $name);
    }
}
