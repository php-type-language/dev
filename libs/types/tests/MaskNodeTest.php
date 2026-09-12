<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;
use TypeLang\Type\MaskNode;
use TypeLang\Type\WildcardNode;

/**
 * Tests for the name of a constant written in part.
 */
final class MaskNodeTest extends TestCase
{
    /**
     * @param list<non-empty-string> $items each of which is either a literal
     *        segment or the "*" of a wildcard
     */
    private static function mask(array $items): MaskNode
    {
        $parts = [];

        foreach ($items as $item) {
            $parts[] = $item === '*' ? new WildcardNode() : new Identifier($item);
        }

        return new MaskNode($parts);
    }

    /**
     * @return iterable<non-empty-string, array{list<non-empty-string>, non-empty-string, list<non-empty-string>}>
     */
    public static function provideMasks(): iterable
    {
        yield 'a wildcard alone' => [['*'], '*', []];
        yield 'a trailing wildcard' => [['BAR_', '*'], 'BAR_*', ['BAR_']];
        yield 'a leading wildcard' => [['*', '_BAR'], '*_BAR', ['_BAR']];
        yield 'a wildcard in between' => [['A', '*', 'B'], 'A*B', ['A', 'B']];
        yield 'several wildcards' => [
            ['BAR', '*', 'BAZ', '*', 'SOME'],
            'BAR*BAZ*SOME',
            ['BAR', 'BAZ', 'SOME'],
        ];
        yield 'wildcards all around' => [['*', 'A', '*'], '*A*', ['A']];
    }

    /**
     * @param list<non-empty-string> $items
     * @param non-empty-string $expected
     * @param list<non-empty-string> $segments
     */
    #[Test]
    #[DataProvider('provideMasks')]
    public function aMaskIsWrittenTheWayItIsRead(array $items, string $expected, array $segments): void
    {
        self::assertSame($expected, self::mask($items)->toString());
        self::assertSame($expected, (string) self::mask($items));
    }

    /**
     * @param list<non-empty-string> $items
     * @param non-empty-string $expected
     * @param list<non-empty-string> $segments
     */
    #[Test]
    #[DataProvider('provideMasks')]
    public function segmentsAreTheLiteralPartsAlone(array $items, string $expected, array $segments): void
    {
        $mask = self::mask($items);

        self::assertSame($segments, $mask->getSegmentsAsStrings());

        $identifiers = [];

        foreach ($mask->getSegments() as $identifier) {
            $identifiers[] = $identifier->value;
        }

        self::assertSame($segments, $identifiers);
    }

    #[Test]
    public function everyPartOfAMaskIsKeptInTheOrderItIsWrittenIn(): void
    {
        $mask = self::mask(['A', '*', 'B']);

        self::assertCount(3, $mask);
        self::assertInstanceOf(Identifier::class, $mask->items[0]);
        self::assertInstanceOf(WildcardNode::class, $mask->items[1]);
        self::assertInstanceOf(Identifier::class, $mask->items[2]);
    }
}
