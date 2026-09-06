<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

use Phplrt\Contracts\Parser\Exception\ParserExceptionInterface;
use Phplrt\Contracts\Parser\Exception\RuntimeExceptionInterface;
use Phplrt\Contracts\Source\ReadableInterface;
use Phplrt\Parser\Analysis\Mode;
use Phplrt\Parser\Analysis\Result\FailureResult;
use Phplrt\Parser\Analysis\Result\SuccessfulResult;
use Phplrt\Parser\Parser as ParserRuntime;
use TypeLang\Parser\TypeParserFeatures;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\TypeNode;

/**
 * The compiled TypeLang grammar, bound to the set of features the source is
 * recognized with.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser
 *
 * @template-extends CompilerExecutor<TypeNode>
 */
final class Executor extends CompilerExecutor
{
    public function __construct(
        /**
         * @api this property is accessible inside the grammar reducers
         */
        protected readonly TypeParserFeatures $features,
    ) {
        parent::__construct();
    }

    /**
     * Reads as much of the given source as the grammar describes and reports
     * what it has been made of.
     *
     * @throws ParserExceptionInterface in case of the parser cannot be built
     * @throws RuntimeExceptionInterface in case of the source cannot be read
     * @throws \Throwable in case of an internal error occurs
     */
    public function analyze(
        ReadableInterface $source,
        Mode $mode = Mode::Tolerant,
    ): SuccessfulResult|FailureResult {
        $parser = $this->parser;

        if (!$parser instanceof ParserRuntime) {
            throw new \LogicException('The compiled grammar must be built on top of the phplrt parser');
        }

        return $parser->analyze($source, $mode);
    }

    /**
     * @param non-empty-string $value
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of value parsing error occurs
     */
    protected function createDoubleQuotedString(string $value, int $offset = 0): StringLiteralNode
    {
        if (\strlen($value) < 2) {
            throw new \InvalidArgumentException('Could not parse non-quoted string');
        }

        return new StringLiteralNode(
            value: StringDecoder::decode(\substr($value, 1, -1)),
            raw: $value,
            offset: $offset,
        );
    }

    /**
     * @param non-empty-string $value
     * @param int<0, max> $offset
     * @throws \InvalidArgumentException in case of value parsing error occurs
     */
    protected function createSingleQuotedString(string $value, int $offset = 0): StringLiteralNode
    {
        if (\strlen($value) < 2) {
            throw new \InvalidArgumentException('Could not parse non-quoted string');
        }

        return new StringLiteralNode(
            value: StringDecoder::unescape(\substr($value, 1, -1)),
            raw: $value,
            offset: $offset,
        );
    }

    /**
     * Parse raw integer literal string value: Decimal, hexadecimal
     * (like a "0xFF"), octal (like a "0o17" or a "017") and binary
     * (like a "0b1010") ones.
     *
     * @param int<0, max> $offset
     */
    protected function createInt(string $value, int $offset = 0): IntLiteralNode
    {
        [$negative, $decimal] = self::split($value);

        $inverse = '-' . $decimal;

        if ($negative) {
            if ((string) \PHP_INT_MIN === $inverse) {
                return new IntLiteralNode(\PHP_INT_MIN, $value, $inverse, $offset);
            }

            /** @phpstan-ignore-next-line : An "$inverse" variable contain numeric-string */
            return new IntLiteralNode((int) $inverse, $value, $inverse, $offset);
        }

        return new IntLiteralNode((int) $decimal, $value, $decimal, $offset);
    }

    /**
     * @return array{bool, numeric-string}
     */
    private static function split(string $literal): array
    {
        $literal = \str_replace('_', '', $literal);

        if ($negative = ($literal[0] === '-')) {
            $literal = \substr($literal, 1);
        }

        // One of: [ 0123, 0o23, 0x00, 0b01 ]
        if ($literal[0] === '0' && isset($literal[1])) {
            /** @var array{bool, numeric-string} */
            return [$negative, match ($literal[1]) {
                // hexadecimal
                'x', 'X' => \base_convert(\substr($literal, 2), 16, 10),
                // binary
                'b', 'B' => \base_convert(\substr($literal, 2), 2, 10),
                // octal
                'o', 'O' => \base_convert(\substr($literal, 2), 8, 10),
                // octal (legacy)
                default => \base_convert($literal, 8, 10),
            }];
        }

        /** @var array{bool, numeric-string} */
        return [$negative, $literal];
    }

    /**
     * @param int<0, max> $offset
     */
    protected function createBool(string $value, int $offset = 0): BoolLiteralNode
    {
        return new BoolLiteralNode(
            \strtolower($value) === 'true',
            $value,
            $offset,
        );
    }
}
