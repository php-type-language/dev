<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Template;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Template\TemplateParameterNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Name;
use TypeLang\Type\Template\TemplateBoundNode;
use TypeLang\Type\Template\TemplateParameterNode;

#[CoversClass(TemplateParameterNodePrinter::class)]
final class TemplateParameterNodePrinterTest extends NodePrinterTestCase
{
    public function testNameOnly(): void
    {
        self::assertSame('T', self::pretty(new TemplateParameterNode(new Name('T'))));
    }

    public function testUpperBoundFollowsTheName(): void
    {
        $parameter = new TemplateParameterNode(
            new Name('T'),
            new TemplateBoundNode(new Name('of'), self::named('int')),
        );

        self::assertSame('T of int', self::pretty($parameter));
    }

    public function testLowerBoundFollowsTheUpperOne(): void
    {
        $parameter = new TemplateParameterNode(
            new Name('T'),
            new TemplateBoundNode(new Name('of'), self::named('int')),
            new TemplateBoundNode(new Name('super'), self::named('float')),
        );

        self::assertSame('T of int super float', self::pretty($parameter));
    }

    public function testLowerBoundAlone(): void
    {
        $parameter = new TemplateParameterNode(
            new Name('T'),
            null,
            new TemplateBoundNode(new Name('super'), self::named('float')),
        );

        self::assertSame('T super float', self::pretty($parameter));
    }

    public function testDefaultFollowsTheEqualsSign(): void
    {
        $parameter = new TemplateParameterNode(new Name('T'), null, null, self::named('int'));

        self::assertSame('T = int', self::pretty($parameter));
    }

    public function testVarianceIsWrittenBeforeTheName(): void
    {
        $parameter = new TemplateParameterNode(new Name('T'), null, null, null, new Name('covariant'));

        self::assertSame('covariant T', self::pretty($parameter));
    }

    public function testEverythingAtOnce(): void
    {
        $parameter = new TemplateParameterNode(
            new Name('T'),
            new TemplateBoundNode(new Name('of'), self::named('int')),
            new TemplateBoundNode(new Name('super'), self::named('float')),
            self::named('int'),
            new Name('in'),
        );

        self::assertSame('in T of int super float = int', self::pretty($parameter));
    }
}
