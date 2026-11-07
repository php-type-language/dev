<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Template;

use TypeLang\Printer\PrinterOptions\CommaSpacing;
use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Template\TemplateListNodePrinter;
use TypeLang\Printer\PrinterOptions;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Identifier;
use TypeLang\Type\Template\TemplateListNode;
use TypeLang\Type\Template\TemplateParameterListNode;
use TypeLang\Type\Template\TemplateParameterNode;
use TypeLang\Type\Template\TypedTemplateArgumentNode;
use TypeLang\Type\Template\WildcardTemplateArgumentNode;

#[CoversClass(TemplateListNodePrinter::class)]
final class TemplateListNodePrinterTest extends NodePrinterTestCase
{
    private static function arguments(): TemplateListNode
    {
        return new TemplateListNode([
            new TypedTemplateArgumentNode(self::named('int')),
            new TypedTemplateArgumentNode(self::named('string')),
        ]);
    }

    public function testArgumentsAreWrittenInAngleBrackets(): void
    {
        self::assertSame('<int, string>', self::pretty(self::arguments()));
    }

    public function testArgumentsAreMultilineAfterTheThreshold(): void
    {
        self::assertSame(
            "<\n    int,\n    string\n>",
            self::pretty(self::arguments(), new PrinterOptions(maxInlineTemplateItems: 1)),
        );
    }

    public function testArgumentsAreSingleLineUpToTheThreshold(): void
    {
        $options = new PrinterOptions(maxInlineTemplateItems: 2);

        self::assertSame('<int, string>', self::pretty(self::arguments(), $options));
    }

    public function testNoSpaceAfterCommaBetweenArguments(): void
    {
        $options = new PrinterOptions(commaSpacing: CommaSpacing::Compact);

        self::assertSame('<int,string>', self::pretty(self::arguments(), $options));
    }

    public function testWildcardIsAnArgumentOfItsOwn(): void
    {
        $list = new TemplateListNode([
            new TypedTemplateArgumentNode(self::named('int')),
            new WildcardTemplateArgumentNode(),
        ]);

        self::assertSame('<int, *>', self::pretty($list));
    }

    public function testArgumentsMayBeDeclaredParameters(): void
    {
        $list = new TemplateListNode([
            new TemplateParameterNode(new Identifier('T')),
            new TypedTemplateArgumentNode(self::named('int')),
        ]);

        self::assertSame('<T, int>', self::pretty($list));
    }

    public function testParametersAreWrittenInAngleBrackets(): void
    {
        $list = new TemplateParameterListNode([
            new TemplateParameterNode(new Identifier('T')),
            new TemplateParameterNode(new Identifier('U')),
        ]);

        self::assertSame('<T, U>', self::pretty($list));
    }

    public function testParametersAreMultilineAfterTheThreshold(): void
    {
        $list = new TemplateParameterListNode([
            new TemplateParameterNode(new Identifier('T')),
            new TemplateParameterNode(new Identifier('U')),
        ]);

        self::assertSame("<\n    T,\n    U\n>", self::pretty($list, new PrinterOptions(maxInlineTemplateItems: 1)));
    }
}
