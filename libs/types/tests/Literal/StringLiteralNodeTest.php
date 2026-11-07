<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Literal;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\StringLiteralKind;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\Tests\TestCase;

final class StringLiteralNodeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{string, StringLiteralKind, non-empty-string}>
     */
    public static function derivedRawValuesDataProvider(): iterable
    {
        yield 'double quoted plain' => ['example', StringLiteralKind::DoubleQuoted, '"example"'];
        yield 'double quoted empty' => ['', StringLiteralKind::DoubleQuoted, '""'];
        yield 'double quoted double quote' => ['a"b', StringLiteralKind::DoubleQuoted, '"a\"b"'];
        yield 'double quoted single quote' => ["a'b", StringLiteralKind::DoubleQuoted, '"a\'b"'];
        yield 'double quoted backslash' => ['a\\b', StringLiteralKind::DoubleQuoted, '"a\\\\b"'];
        yield 'double quoted line break' => ["a\nb", StringLiteralKind::DoubleQuoted, '"a\nb"'];
        yield 'double quoted escape' => ["\e", StringLiteralKind::DoubleQuoted, '"\033"'];

        yield 'single quoted plain' => ['example', StringLiteralKind::SingleQuoted, "'example'"];
        yield 'single quoted empty' => ['', StringLiteralKind::SingleQuoted, "''"];
        yield 'single quoted single quote' => ["a'b", StringLiteralKind::SingleQuoted, "'a\\'b'"];
        yield 'single quoted double quote' => ['a"b', StringLiteralKind::SingleQuoted, "'a\"b'"];
        yield 'single quoted backslash' => ['a\\b', StringLiteralKind::SingleQuoted, "'a\\\\b'"];
        yield 'single quoted line break' => ["a\nb", StringLiteralKind::SingleQuoted, "'a\nb'"];
        yield 'single quoted tab' => ["a\tb", StringLiteralKind::SingleQuoted, "'a\tb'"];
        yield 'single quoted null byte' => ["a\0b", StringLiteralKind::SingleQuoted, "'a\0b'"];
    }

    /**
     * @param non-empty-string $raw
     */
    #[Test]
    #[DataProvider('derivedRawValuesDataProvider')]
    public function constructorEscapesTheDerivedRawValue(string $value, StringLiteralKind $kind, string $raw): void
    {
        $node = new StringLiteralNode($value, $kind);

        self::assertSame($raw, $node->raw);
        self::assertSame($value, $node->value);
    }

    #[Test]
    public function kindIsSingleQuotedByDefault(): void
    {
        self::assertSame("'a'", (new StringLiteralNode('a'))->raw);
    }

    #[Test]
    public function explicitRawValueIsKept(): void
    {
        self::assertSame('"a"', (new StringLiteralNode('a', raw: '"a"'))->raw);
    }
}
