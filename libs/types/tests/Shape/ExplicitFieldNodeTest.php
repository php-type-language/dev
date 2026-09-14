<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\ClassConstMaskNode;
use TypeLang\Type\ClassConstNode;
use TypeLang\Type\ConstMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\MaskNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\ComplexFieldNode;
use TypeLang\Type\Shape\ExplicitFieldNode;
use TypeLang\Type\Shape\FieldNode;
use TypeLang\Type\Shape\ImplicitFieldNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Shape\ScalarFieldNode;
use TypeLang\Type\Tests\TestCase;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WildcardNode;

final class ExplicitFieldNodeTest extends TestCase
{
    private static function type(string $name = 'Example'): NamedTypeNode
    {
        return new NamedTypeNode(Name::createFromString($name));
    }

    /**
     * @return iterable<non-empty-string, array{ExplicitFieldNode}>
     */
    public static function provideExplicitFields(): iterable
    {
        yield 'named' => [new NamedFieldNode(new Identifier('key'), self::type())];
        yield 'scalar' => [new ScalarFieldNode(new IntLiteralNode(42), self::type())];
        yield 'complex' => [
            new ComplexFieldNode(
                new ClassConstNode(Name::createFromString('Vendor\Status'), new Identifier('OK')),
                self::type(),
            ),
        ];
    }

    /**
     * @return iterable<non-empty-string, array{ComplexFieldNode}>
     */
    public static function provideComplexFields(): iterable
    {
        yield 'class const' => [
            new ComplexFieldNode(
                new ClassConstNode(Name::createFromString('Vendor\Status'), new Identifier('OK')),
                self::type(),
            ),
        ];

        yield 'class const mask' => [
            new ComplexFieldNode(
                new ClassConstMaskNode(
                    Name::createFromString('Vendor\Status'),
                    new MaskNode([new Identifier('IS_'), new WildcardNode()]),
                ),
                self::type(),
            ),
        ];

        yield 'const mask' => [
            new ComplexFieldNode(
                new ConstMaskNode(
                    new MaskNode([new Identifier('STATUS_'), new WildcardNode()]),
                    Name::createFromString('Vendor'),
                ),
                self::type(),
            ),
        ];
    }

    #[Test]
    #[DataProvider('provideComplexFields')]
    public function complexFieldIsKeyedByAType(ComplexFieldNode $field): void
    {
        self::assertInstanceOf(TypeNode::class, $field->key);
    }

    #[Test]
    #[DataProvider('provideExplicitFields')]
    public function explicitFieldIsRequiredByDefault(ExplicitFieldNode $field): void
    {
        self::assertFalse($field->isOptional);
    }

    #[Test]
    #[DataProvider('provideExplicitFields')]
    public function explicitFieldIsAFieldNode(ExplicitFieldNode $field): void
    {
        self::assertInstanceOf(FieldNode::class, $field);
        self::assertInstanceOf(TypeNode::class, $field->type);
    }

    #[Test]
    public function optionalFieldStoresTheFlag(): void
    {
        $field = new NamedFieldNode(new Identifier('key'), self::type(), true);

        self::assertTrue($field->isOptional);
    }

    #[Test]
    public function implicitFieldHasNoKey(): void
    {
        $field = new ImplicitFieldNode(self::type());

        self::assertInstanceOf(FieldNode::class, $field);
        self::assertNotInstanceOf(ExplicitFieldNode::class, $field);
    }

    #[Test]
    public function fieldTypeIsMutable(): void
    {
        $field = new NamedFieldNode(new Identifier('key'), self::type('A'));
        $field->type = self::type('B');

        self::assertSame('B', $field->type->name->toString());
    }
}
