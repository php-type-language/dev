<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Template;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Printer\NodePrinter\Template\TypedTemplateArgumentNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Template\TypedTemplateArgumentNode;
use TypeLang\Type\Template\VarianceNode;
use TypeLang\Type\Template\VarianceNode\VarianceKind;

#[CoversClass(TypedTemplateArgumentNodePrinter::class)]
final class TypedTemplateArgumentNodePrinterTest extends NodePrinterTestCase
{
    public function testArgumentIsPrintedAsItsType(): void
    {
        self::assertSame('int', self::pretty(new TypedTemplateArgumentNode(self::named('int'))));
    }

    /**
     * @return iterable<non-empty-string, array{VarianceKind, non-empty-string}>
     */
    public static function varianceDataProvider(): iterable
    {
        yield 'covariant' => [VarianceKind::Covariant, 'covariant int'];
        yield 'contravariant' => [VarianceKind::Contravariant, 'contravariant int'];
        yield 'invariant' => [VarianceKind::Invariant, 'invariant int'];
    }

    #[DataProvider('varianceDataProvider')]
    public function testVarianceIsWrittenBeforeTheType(VarianceKind $kind, string $expected): void
    {
        $argument = new TypedTemplateArgumentNode(self::named('int'), new VarianceNode($kind));

        self::assertSame($expected, self::pretty($argument));
    }
}
