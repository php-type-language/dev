<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Internal;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Parser\Internal\SubParser\ClassConstMaskParser;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;

final class MaskParserTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{non-empty-string, list<array{string, int<0, max>}>}>
     */
    public static function maskDataProvider(): iterable
    {
        yield 'star alone' => ['*', [['*', 0]]];
        yield 'suffix' => ['PHP_*', [['PHP_', 0], ['*', 4]]];
        yield 'prefix' => ['*_SUFFIX', [['*', 0], ['_SUFFIX', 1]]];
        yield 'inside' => ['A*B', [['A', 0], ['*', 1], ['B', 2]]];
        yield 'both ends' => ['*A*', [['*', 0], ['A', 1], ['*', 2]]];
        yield 'many stars' => ['A*B*C*', [['A', 0], ['*', 1], ['B', 2], ['*', 3], ['C', 4], ['*', 5]]];
        yield 'multibyte word' => ['Фу*', [['Фу', 0], ['*', 4]]];
    }

    /**
     * @param non-empty-string $raw
     * @param list<array{string, int<0, max>}> $parts
     */
    #[DataProvider('maskDataProvider')]
    public function testMaskIsCutIntoWordsAndStars(string $raw, array $parts): void
    {
        $mask = (new ClassConstMaskParser())->parse($raw);

        self::assertSame($raw, $mask->toString());
        self::assertSame($parts[0][1], $mask->offset);
        self::assertSame(
            $parts,
            \array_map(static fn(object $item): array => [$item->toString(), $item->offset], $mask->items),
        );

        foreach ($mask->items as $k => $item) {
            self::assertSame($parts[$k][0] === '*', $item instanceof WildcardMaskNode);
        }
    }

    public function testOffsetOfAMaskIsAddedToItsItems(): void
    {
        $mask = (new ClassConstMaskParser())->parse('A*B', 10);

        self::assertSame(10, $mask->offset);
        self::assertSame([10, 11, 12], \array_map(static fn(object $item): int => $item->offset, $mask->items));
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, string|null, bool, non-empty-string}>
     */
    public static function globalDataProvider(): iterable
    {
        yield 'no namespace' => ['PHP_*', null, false, 'PHP_*'];
        yield 'root' => ['\\PHP_*', null, true, 'PHP_*'];
        yield 'root star' => ['\\*', null, true, '*'];
        yield 'namespace' => ['Foo\\Bar\\BAZ_*', 'Foo\\Bar', false, 'BAZ_*'];
        yield 'fully qualified namespace' => ['\\Foo\\BAR_*', '\\Foo', true, 'BAR_*'];
        yield 'whole namespace' => ['Path\\To\\*', 'Path\\To', false, '*'];
    }

    /**
     * @param non-empty-string $raw
     * @param non-empty-string $mask
     */
    #[DataProvider('globalDataProvider')]
    public function testGlobalMaskKeepsItsNamespace(string $raw, ?string $namespace, bool $isFullyQualified, string $mask): void
    {
        $type = (new ClassConstMaskParser())->parseGlobal($raw);

        self::assertSame($namespace, $type->namespace?->toString());
        self::assertSame($isFullyQualified, $type->isFullyQualified);
        self::assertSame($mask, $type->mask->toString());
    }

    public function testMaskOfAGlobalConstantStartsBehindTheNamespace(): void
    {
        $type = (new ClassConstMaskParser())->parseGlobal('Foo\\BAR_*', 5);

        self::assertSame(5, $type->offset);
        self::assertSame(5, $type->namespace?->offset);
        self::assertSame(9, $type->mask->offset);
    }
}
