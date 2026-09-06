<?php

declare(strict_types=1);

namespace TypeLang\Parser\Literal;

use TypeLang\Parser\Internal\StringParser;
use TypeLang\Type\Literal\StringLiteralNode;

final class StringLiteralParser
{
    /**
     * Parse raw quoted string literal value.
     *
     * @throws \InvalidArgumentException in case of value parsing error occurs
     */
    public static function parse(string $value): StringLiteralNode
    {
        if (\strlen($value) < 2) {
            throw new \InvalidArgumentException('Could not parse non-quoted string');
        }

        if ($value[0] === '"') {
            return self::createFromDoubleQuotedString($value);
        }

        return self::createFromSingleQuotedString($value);
    }

    /**
     * @param non-empty-string $value
     * @throws \InvalidArgumentException in case of value parsing error occurs
     */
    public static function createFromDoubleQuotedString(string $value): StringLiteralNode
    {
        if (\strlen($value) < 2) {
            throw new \InvalidArgumentException('Could not parse non-quoted string');
        }

        return new StringLiteralNode(
            value: StringParser::parse(\substr($value, 1, -1)),
            raw: $value,
        );
    }

    /**
     * @param non-empty-string $value
     * @throws \InvalidArgumentException in case of value parsing error occurs
     */
    public static function createFromSingleQuotedString(string $value): StringLiteralNode
    {
        if (\strlen($value) < 2) {
            throw new \InvalidArgumentException('Could not parse non-quoted string');
        }

        return new StringLiteralNode(
            value: \strtr(\substr($value, 1, -1), [
                "\'" => "'",
                '\\\\' => '\\',
            ]),
            raw: $value,
        );
    }
}
