<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Constant;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Constant\ClassConstTypeNode;
use TypeLang\Type\Identifier;
use TypeLang\Type\Tests\TestCase;

final class ClassConstTypeNodeTest extends TestCase
{
    #[Test]
    public function fromBuildsTheClassAndTheConstantFromStrings(): void
    {
        $node = ClassConstTypeNode::from('\Some\Any', 'CONST_NAME', 5);

        self::assertSame('\Some\Any', $node->class->toString());
        self::assertSame('CONST_NAME', $node->constant->value);
        self::assertSame(5, $node->offset);
    }

    #[Test]
    public function fromKeepsAnExistingIdentifier(): void
    {
        $constant = new Identifier('CONST_NAME');

        self::assertSame($constant, ClassConstTypeNode::from('Some', $constant)->constant);
    }
}
