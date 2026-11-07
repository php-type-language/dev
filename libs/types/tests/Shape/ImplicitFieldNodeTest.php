<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Shape;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Shape\ComplexFieldNode;
use TypeLang\Type\Shape\ExplicitFieldNode;
use TypeLang\Type\Shape\ImplicitFieldNode;
use TypeLang\Type\Shape\NamedFieldNode;
use TypeLang\Type\Shape\ScalarFieldNode;
use TypeLang\Type\Tests\TestCase;

final class ImplicitFieldNodeTest extends TestCase
{
    #[Test]
    public function typeAndOffsetAreStored(): void
    {
        $type = NamedTypeNode::from('int');
        $field = new ImplicitFieldNode($type, 5);

        self::assertSame($type, $field->type);
        self::assertSame(5, $field->offset);
    }

    #[Test]
    public function fieldWithNoKeyCannotBeOptional(): void
    {
        self::assertFalse(\property_exists(ImplicitFieldNode::class, 'isOptional'));
    }

    #[Test]
    public function fieldWithAKeyCanBeOptional(): void
    {
        $classes = [
            ExplicitFieldNode::class,
            NamedFieldNode::class,
            ScalarFieldNode::class,
            ComplexFieldNode::class,
        ];

        foreach ($classes as $class) {
            self::assertTrue(\property_exists($class, 'isOptional'), $class);
        }
    }
}
