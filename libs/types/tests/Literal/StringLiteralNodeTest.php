<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\Tests\TestCase;

final class StringLiteralNodeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{string, non-empty-string}>
     */
    public static function derivedRawValuesDataProvider(): iterable
    {
        yield 'plain' => ['example', '"example"'];
        yield 'double quote' => ['a"b', '"a\"b"'];
        yield 'single quote' => ["a'b", '"a\'b"'];
        yield 'backslash' => ['a\\b', '"a\\\\b"'];
        yield 'backslash before quote' => ['\\"', '"\\\\\\""'];
        yield 'line break' => ["a\nb", '"a\nb"'];
        yield 'empty' => ['', '""'];
    }

    #[Test]
    #[DataProvider('derivedRawValuesDataProvider')]
    public function constructorEscapesTheDerivedRawValue(string $value, string $raw): void
    {
        $node = new StringLiteralNode($value);

        self::assertSame($raw, $node->raw);
        self::assertSame($value, $node->value);
    }

    #[Test]
    public function explicitRawValueIsKept(): void
    {
        self::assertSame("'a'", (new StringLiteralNode('a', "'a'"))->raw);
    }
}
