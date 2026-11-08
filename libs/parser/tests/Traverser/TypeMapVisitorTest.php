<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\Traverser;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Parser\Tests\TestCase;
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\TypeMapVisitor;
use TypeLang\Type\Callable\CallableTypeNode;
use TypeLang\Type\Constant\ClassConstMaskNode;
use TypeLang\Type\Constant\ClassConstNode;
use TypeLang\Type\Constant\ConstMaskNode;
use TypeLang\Type\Name;
use TypeLang\Type\Constant\MaskNode;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\LogicalTypeNode\UnionTypeNode;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;

final class TypeMapVisitorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        self::markTestIncomplete('TODO');
    }

    private function alias(string $to = 'Aliased'): TypeMapVisitor
    {
        return new TypeMapVisitor(static fn(Name $name): Name => Name::createFromString($to));
    }

    #[Test]
    public function theNameOfANamedTypeIsTransformed(): void
    {
        $node = new NamedTypeNode(Name::createFromString('Example'));

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('Aliased', $node->name->unqualified);
    }

    #[Test]
    public function theNameOfACallableTypeIsTransformed(): void
    {
        $node = new CallableTypeNode(Name::createFromString('callable'));

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('Aliased', $node->name->unqualified);
    }

    #[Test]
    public function theNamespaceOfAConstMaskIsTransformed(): void
    {
        $node = new ConstMaskNode(
            new MaskNode([new Name('SOME_'), new WildcardMaskNode()]),
            Name::createFromString('Vendor'),
        );

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertInstanceOf(Name::class, $node->namespaceOrFullyQualified);
        self::assertSame('Aliased', $node->namespaceOrFullyQualified->toString());
    }

    #[Test]
    public function aConstMaskWithoutANamespaceIsLeftAlone(): void
    {
        $node = new ConstMaskNode(new MaskNode([new WildcardMaskNode(), new Name('_SOME')]));

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertFalse($node->namespaceOrFullyQualified);
    }

    #[Test]
    public function theClassOfAClassConstIsTransformed(): void
    {
        $node = new ClassConstNode(
            class: Name::createFromString('Example'),
            constant: new Name('CONSTANT'),
        );

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('Aliased', $node->class->toString());
    }

    #[Test]
    public function theConstantOfAClassConstIsNotTransformed(): void
    {
        $node = new ClassConstNode(
            class: Name::createFromString('Example'),
            constant: new Name('CONSTANT'),
        );

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('CONSTANT', $node->constant->toString());
    }

    #[Test]
    public function theClassOfAClassConstMaskIsTransformed(): void
    {
        $node = new ClassConstMaskNode(Name::createFromString('Example'));

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame('Aliased', $node->class->toString());
    }

    #[Test]
    public function theNullResultKeepsTheOriginalName(): void
    {
        $expected = Name::createFromString('Example');
        $node = new NamedTypeNode($expected);

        Traverser::new([new TypeMapVisitor(static fn(Name $name): ?Name => null)])
            ->traverse([$node]);

        self::assertSame($expected, $node->name);
    }

    #[Test]
    public function theTransformationIsAppliedToEveryNestedType(): void
    {
        $node = new UnionTypeNode([
            new NamedTypeNode(Name::createFromString('A')),
            new NamedTypeNode(Name::createFromString('B'))
        ]);

        Traverser::new([$this->alias()])->traverse([$node]);

        self::assertSame(
            ['Aliased', 'Aliased'],
            \array_map(
                static fn(NamedTypeNode $type): string => $type->name->unqualified,
                $node->statements,
            ),
        );
    }

    #[Test]
    public function theTransformationReceivesTheOriginalName(): void
    {
        $received = [];

        $visitor = new TypeMapVisitor(static function (Name $name) use (&$received): Name {
            $received[] = $name->toString();

            return $name;
        });

        Traverser::new([$visitor])->traverse([
            new NamedTypeNode(Name::createFromString('Foo\\Bar')),
        ]);

        self::assertSame(['Foo\\Bar'], $received);
    }

    #[Test]
    public function theTransformationIsNotAppliedToAnArbitraryName(): void
    {
        $received = [];

        $visitor = new TypeMapVisitor(static function (Name $name) use (&$received): Name {
            $received[] = $name->toString();

            return $name;
        });

        Traverser::new([$visitor])->traverse([Name::createFromString('Example')]);

        self::assertSame([], $received);
    }

    #[Test]
    public function theTransformedNameIsVisitedInsteadOfTheOriginalOne(): void
    {
        $node = new NamedTypeNode(Name::createFromString('Example'));
        $expected = Name::createFromString('Aliased');

        Traverser::new([new TypeMapVisitor(static fn(Name $name): Name => $expected)])
            ->traverse([$node]);

        self::assertSame($expected, $node->name);
    }
}
