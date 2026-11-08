<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Constant;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Name;
use TypeLang\Type\Tests\TestCase;

final class ClassConstTypeNodeTest extends TestCase
{
    #[Test]
    public function constructorKeepsTheClassTheConstantAndTheOffset(): void
    {
        $class = new Name('\Some\Any');
        $constant = new Name('CONST_NAME');

        $node = new ClassConstTypeNode($class, $constant, 5);

        self::assertSame($class, $node->class);
        self::assertSame($constant, $node->constant);
        self::assertSame('\Some\Any', $node->class->toString());
        self::assertSame('CONST_NAME', $node->constant->toString());
        self::assertSame(5, $node->offset);
    }
}
