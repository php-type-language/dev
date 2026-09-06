<?php

declare(strict_types=1);

namespace TypeLang\Parser\Exception;

use Phplrt\Contracts\Lexer\Channel;
use Phplrt\Lexer\Token\Printer\PrettyTokenPrinter;
use Phplrt\Lexer\Token\Token;
use Phplrt\Position\PositionFactory;
use Phplrt\Source\StringSource;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Exception
 */
final class Formatter
{
    /**
     * @return non-empty-string
     */
    public static function token(string $expr): string
    {
        return match ($expr) {
            "\0", '' => 'end of input',
            '"' => 'double quote (")',
            default => \sprintf('"%s"', \addcslashes($expr, '"')),
        };
    }

    public static function source(string $statement): string
    {
        $statement = \trim($statement);

        if ($statement === '') {
            return '<empty statement>';
        }

        $printer = new PrettyTokenPrinter();

        return $printer->printValue(new Token(
            id: 0,
            name: null,
            channel: Channel::Default,
            value: $statement,
        ));
    }

    /**
     * @param int<0, max> $offset
     * @return non-empty-string
     */
    public static function suffix(string $statement, int $offset): string
    {
        if (\str_contains($statement, "\n")) {
            $positions = new PositionFactory();

            $pos = $positions->createFromOffset(
                source: StringSource::createFromString($statement),
                offset: $offset,
            );

            return \sprintf('on line %d at column %d', $pos->line, $pos->column);
        }

        return \sprintf('at column %d', $offset + 1);
    }
}
