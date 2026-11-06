<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Literal\BoolLiteralNode;
use TypeLang\Type\Literal\FloatLiteralNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Literal\NullLiteralNode;
use TypeLang\Type\Literal\StringLiteralNode;
use TypeLang\Type\Name\TypeName;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\ComplexFieldNode;
use TypeLang\Type\Tests\TestCase;
use TypeLang\Type\TypeNode;

final class ComplexFieldNodeTest extends TestCase
{
    /**
     * @return iterable<non-empty-string, array{TypeNode}>
     */
    public static function provideLiteralKeys(): iterable
    {
        yield 'int' => [new IntLiteralNode(0)];
        yield 'string' => [new StringLiteralNode('key')];
        yield 'bool' => [new BoolLiteralNode(true)];
        yield 'null' => [new NullLiteralNode()];
    }

    #[Test]
    #[DataProvider('provideLiteralKeys')]
    public function literalKeyIsRejected(TypeNode $key): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new ComplexFieldNode($key, new NamedTypeNode(TypeName::from('T')));
    }

    #[Test]
    public function typeKeyIsAccepted(): void
    {
        $key = new NamedTypeNode(TypeName::from('User'));
        $field = new ComplexFieldNode($key, new NamedTypeNode(TypeName::from('T')));

        self::assertSame($key, $field->key);
    }

    #[Test]
    public function floatKeyIsAccepted(): void
    {
        $key = new FloatLiteralNode(1.5);

        self::assertSame($key, (new ComplexFieldNode($key, new NamedTypeNode(TypeName::from('T'))))->key);
    }
}
