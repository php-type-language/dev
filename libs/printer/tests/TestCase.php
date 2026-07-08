<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeParserInterface;
use TypeLang\Type\TypeNode;

#[Group('type-lang/printer')]
abstract class TestCase extends BaseTestCase
{
    private static ?TypeParserInterface $parser = null;

    /**
     * Parses a type statement into an AST node used as printer input.
     *
     * @param non-empty-string $type
     */
    protected static function parse(string $type): TypeNode
    {
        return (self::$parser ??= new TypeParser())->parse($type);
    }
}
