<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Attribute\AttributeGroupListNode;
use TypeLang\Type\ClassConstMaskNode;
use TypeLang\Type\ClassConstNode;
use TypeLang\Type\ConstMaskNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\ClassConstFieldNode;
use TypeLang\Type\Shape\ClassConstMaskFieldNode;
use TypeLang\Type\Shape\ConstMaskFieldNode;
use TypeLang\Type\Shape\ExplicitFieldNode;
use TypeLang\Type\Shape\FieldNode;
use TypeLang\Type\Shape\ImplicitFieldNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Shape\NumericFieldNode;
use TypeLang\Type\Shape\StringNamedFieldNode;
use TypeLang\Type\Tests\TestCase;
use TypeLang\Type\TypeNode;

final class ExplicitFieldNodeTest extends TestCase
{
    private static function type(string $name = 'Example'): NamedTypeNode
    {
        return new NamedTypeNode(Name::createFromString($name));
    }

    /**
     * @return iterable<non-empty-string, array{ExplicitFieldNode, non-empty-string}>
     */
    public static function provideExplicitFields(): iterable
    {
        yield 'named' => [
            new NamedFieldNode(new Identifier('key'), self::type()),
            'key',
        ];

        yield 'string named' => [
            new StringNamedFieldNode(new StringLiteralNode('some key'), self::type()),
            'some key',
        ];

        yield 'numeric' => [
            new NumericFieldNode(new IntLiteralNode(42), self::type()),
            '42',
        ];

        yield 'class const' => [
            new ClassConstFieldNode(
                new ClassConstNode(Name::createFromString('Vendor\Status'), new Identifier('OK')),
                self::type(),
            ),
            'Vendor\Status::OK',
        ];

        yield 'class const mask' => [
            new ClassConstMaskFieldNode(
                new ClassConstMaskNode(Name::createFromString('Vendor\Status'), new Identifier('IS_')),
                self::type(),
            ),
            'Vendor\Status::IS_*',
        ];

        yield 'const mask' => [
            new ConstMaskFieldNode(new ConstMaskNode(Name::createFromString('Vendor\STATUS_')), self::type()),
            'Vendor\STATUS_*',
        ];
    }

    #[Test]
    #[DataProvider('provideExplicitFields')]
    public function indexMethodReturnsPrettyPrintedKey(ExplicitFieldNode $field, string $index): void
    {
        self::assertSame($index, $field->getIndex());
    }

    #[Test]
    public function indexIsRecalculatedAfterKeyMutation(): void
    {
        $field = new NamedFieldNode(new Identifier('key'), self::type());

        self::assertSame('key', $field->getIndex());

        $field->key = new Identifier('other');

        self::assertSame('other', $field->getIndex(), 'The index must be derived from the current key');
        self::assertSame('other', $field->getIndex());
    }

    #[Test]
    #[DataProvider('provideExplicitFields')]
    public function explicitFieldIsRequiredByDefault(ExplicitFieldNode $field, string $index): void
    {
        self::assertFalse($field->isOptional);
    }

    #[Test]
    #[DataProvider('provideExplicitFields')]
    public function explicitFieldHasNoAttributesByDefault(ExplicitFieldNode $field, string $index): void
    {
        self::assertNull($field->attributes);
    }

    #[Test]
    #[DataProvider('provideExplicitFields')]
    public function explicitFieldIsAFieldNode(ExplicitFieldNode $field, string $index): void
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
    public function attributesAreStored(): void
    {
        $attributes = new AttributeGroupListNode();
        $field = new NamedFieldNode(new Identifier('key'), self::type(), false, $attributes);

        self::assertSame($attributes, $field->attributes);
    }

    #[Test]
    public function implicitFieldHasNoKey(): void
    {
        $field = new ImplicitFieldNode(self::type());

        self::assertInstanceOf(FieldNode::class, $field);
        self::assertNotInstanceOf(ExplicitFieldNode::class, $field);
    }

    #[Test]
    public function classConstMaskFieldWithoutConstantIsStringified(): void
    {
        $field = new ClassConstMaskFieldNode(
            new ClassConstMaskNode(Name::createFromString('Vendor\Status')),
            self::type(),
        );

        self::assertSame('Vendor\Status::*', $field->getIndex());
    }

    #[Test]
    public function numericFieldIndexOfZero(): void
    {
        $field = new NumericFieldNode(new IntLiteralNode(0), self::type());

        self::assertSame('0', $field->getIndex());
    }

    #[Test]
    public function numericFieldIndexOfNegativeKey(): void
    {
        $field = new NumericFieldNode(new IntLiteralNode(-1), self::type());

        self::assertSame('-1', $field->getIndex());
    }

    #[Test]
    public function stringNamedFieldIndexIsTheDecodedValue(): void
    {
        $field = new StringNamedFieldNode(new StringLiteralNode("a\nb"), self::type());

        self::assertSame("a\nb", $field->getIndex());
    }

    #[Test]
    public function fieldTypeIsMutable(): void
    {
        $field = new NamedFieldNode(new Identifier('key'), self::type('A'));
        $field->type = self::type('B');

        self::assertSame('B', $field->type->name->toString());
    }

    #[Test]
    #[DataProvider('provideExplicitFields')]
    public function isReturnsTrueForOwnClass(ExplicitFieldNode $field, string $index): void
    {
        self::assertTrue($field->is($field::class));
        self::assertTrue($field->is(ExplicitFieldNode::class));
        self::assertTrue($field->is(FieldNode::class));
    }

    #[Test]
    #[DataProvider('provideExplicitFields')]
    public function isReturnsFalseForAnotherClass(ExplicitFieldNode $field, string $index): void
    {
        self::assertFalse($field->is(ImplicitFieldNode::class));
    }

    #[Test]
    public function implicitFieldIsNotAnExplicitOne(): void
    {
        $field = new ImplicitFieldNode(self::type());

        self::assertTrue($field->is(ImplicitFieldNode::class));
        self::assertTrue($field->is(FieldNode::class));
        self::assertFalse($field->is(ExplicitFieldNode::class));
    }
}
