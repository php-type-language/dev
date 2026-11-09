<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\NodePrinter\Template;

use PHPUnit\Framework\Attributes\CoversClass;
use TypeLang\Printer\NodePrinter\Template\TemplateParameterNodePrinter;
use TypeLang\Printer\Tests\NodePrinter\NodePrinterTestCase;
use TypeLang\Type\Identifier;
use TypeLang\Type\Template\TemplateBoundNode;
use TypeLang\Type\Template\TemplateParameterNode;
use TypeLang\Type\Template\VarianceNode;
use TypeLang\Type\Template\VarianceNode\VarianceKind;

#[CoversClass(TemplateParameterNodePrinter::class)]
final class TemplateParameterNodePrinterTest extends NodePrinterTestCase
{
    public function testNameOnly(): void
    {
        self::assertSame('T', self::pretty(new TemplateParameterNode(new Identifier('T'))));
    }

    public function testUpperBoundFollowsTheName(): void
    {
        $parameter = new TemplateParameterNode(
            new Identifier('T'),
            new TemplateBoundNode(new Identifier('of'), self::named('int')),
        );

        self::assertSame('T of int', self::pretty($parameter));
    }

    public function testLowerBoundFollowsTheUpperOne(): void
    {
        $parameter = new TemplateParameterNode(
            new Identifier('T'),
            new TemplateBoundNode(new Identifier('of'), self::named('int')),
            new TemplateBoundNode(new Identifier('super'), self::named('float')),
        );

        self::assertSame('T of int super float', self::pretty($parameter));
    }

    public function testLowerBoundAlone(): void
    {
        $parameter = new TemplateParameterNode(
            new Identifier('T'),
            null,
            new TemplateBoundNode(new Identifier('super'), self::named('float')),
        );

        self::assertSame('T super float', self::pretty($parameter));
    }

    public function testDefaultFollowsTheEqualsSign(): void
    {
        $parameter = new TemplateParameterNode(new Identifier('T'), null, null, self::named('int'));

        self::assertSame('T = int', self::pretty($parameter));
    }

    public function testVarianceIsWrittenBeforeTheName(): void
    {
        $parameter = new TemplateParameterNode(
            new Identifier('T'),
            null,
            null,
            null,
            new VarianceNode(VarianceKind::Covariant),
        );

        self::assertSame('covariant T', self::pretty($parameter));
    }

    public function testContravarianceIsWrittenBeforeTheName(): void
    {
        $parameter = new TemplateParameterNode(
            new Identifier('T'),
            null,
            null,
            null,
            new VarianceNode(VarianceKind::Contravariant),
        );

        self::assertSame('contravariant T', self::pretty($parameter));
    }

    public function testEverythingAtOnce(): void
    {
        $parameter = new TemplateParameterNode(
            new Identifier('T'),
            new TemplateBoundNode(new Identifier('of'), self::named('int')),
            new TemplateBoundNode(new Identifier('super'), self::named('float')),
            self::named('int'),
            new VarianceNode(VarianceKind::Contravariant),
        );

        self::assertSame('contravariant T of int super float = int', self::pretty($parameter));
    }
}
