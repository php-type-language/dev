<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal\Literal;

use TypeLang\Parser\Internal\BaseConverter\BaseConverterInterface;
use TypeLang\Type\Literal\IntLiteralKind;
use TypeLang\Type\Literal\IntLiteralNode;

/**
 * A base of the parsers of integer literals, each of them reads one notation.
 *
 * A number out of the range of a native integer is stored in the node in
 * two ways: the value is the nearest bound of the range, the decimal is the
 * exact number.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Parser\Internal
 *
 * @template-implements LiteralParserInterface<IntLiteralNode>
 */
abstract class IntParser implements LiteralParserInterface
{
    /**
     * The base of the digits.
     *
     * @var int<2, 36>
     */
    protected const RADIX = 10;

    /**
     * The length of the notation prefix, such as `0x`.
     *
     * @var int<0, max>
     */
    protected const PREFIX_LENGTH = 0;

    /**
     * The notation of the literals the parser reads.
     *
     * @var IntLiteralKind
     */
    protected const KIND = IntLiteralKind::Decimal;

    public function __construct(
        protected readonly BaseConverterInterface $converter,
    ) {}

    public function parse(string $raw, int $offset = 0): IntLiteralNode
    {
        $number = \str_replace('_', '', $raw);
        $isNegative = $number[0] === '-';
        $digits = \substr($number, ($isNegative || $number[0] === '+' ? 1 : 0) + static::PREFIX_LENGTH);

        \assert($digits !== '');

        // The conversion clamps a number out of the native range to its bound
        $magnitude = \intval($digits, static::RADIX);

        if ($magnitude === \PHP_INT_MAX) {
            return $this->parseBound($raw, $digits, $isNegative, $offset);
        }

        $value = $isNegative ? -$magnitude : $magnitude;

        return new IntLiteralNode(
            value: $value,
            decimal: (string) $value,
            kind: $value === 0 ? IntLiteralKind::Decimal : static::KIND,
            raw: $raw,
            offset: $offset,
        );
    }

    /**
     * Reads a number that is at the bound of the native range or beyond it,
     * the only one the exact decimal digits have to be converted for.
     *
     * @param non-empty-string $raw
     * @param non-empty-string $digits
     * @param int<0, max> $offset
     */
    private function parseBound(string $raw, string $digits, bool $isNegative, int $offset): IntLiteralNode
    {
        $magnitude = $this->convert($digits);

        if ($isNegative) {
            $decimal = '-' . $magnitude;
            $value = $magnitude === (string) \PHP_INT_MAX ? -\PHP_INT_MAX : \PHP_INT_MIN;
        } else {
            $decimal = $magnitude;
            $value = \PHP_INT_MAX;
        }

        \assert(\is_numeric($decimal));

        return new IntLiteralNode(
            value: $value,
            decimal: $decimal,
            kind: static::KIND,
            raw: $raw,
            offset: $offset,
        );
    }

    /**
     * Converts the digits of the notation into the decimal ones.
     *
     * @param non-empty-string $digits without a sign, a prefix and separators
     * @return numeric-string the digits without leading zeros
     */
    protected function convert(string $digits): string
    {
        return $this->converter->toDecimal($digits, static::RADIX);
    }
}
