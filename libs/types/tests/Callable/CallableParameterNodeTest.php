<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Callable;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Callable\DefaultValueNode;
use TypeLang\Type\Literal\IntLiteralNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Tests\TestCase;

final class CallableParameterNodeTest extends TestCase
{

    #[Test]
    public function throwsWhenBothTypeAndNameAreNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CallableParameterNode();
    }

    #[Test]
    public function throwsWhenBothVariadicAndDefaultAreSet(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CallableParameterNode(
            type: new NamedTypeNode(new Name('int')),
            isVariadic: true,
            default: new DefaultValueNode(),
        );
    }

    #[Test]
    public function parameterHasNoDefaultByDefault(): void
    {
        $parameter = new CallableParameterNode(new NamedTypeNode(new Name('int')));

        self::assertNull($parameter->default);
    }

    #[Test]
    public function storesDefaultWithoutValue(): void
    {
        $default = new DefaultValueNode();

        $parameter = new CallableParameterNode(
            type: new NamedTypeNode(new Name('int')),
            default: $default,
        );

        self::assertSame($default, $parameter->default);
        self::assertNull($parameter->default->value);
    }

    #[Test]
    public function storesDefaultWithValue(): void
    {
        $value = new IntLiteralNode(42);

        $parameter = new CallableParameterNode(
            type: new NamedTypeNode(new Name('int')),
            default: new DefaultValueNode($value),
        );

        self::assertSame($value, $parameter->default?->value);
    }
}
