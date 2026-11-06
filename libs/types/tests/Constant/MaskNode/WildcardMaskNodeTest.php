<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Constant\MaskNode;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Node;
use TypeLang\Type\Tests\TestCase;
use TypeLang\Type\TypeNode;

/**
 * Tests for the asterisk standing in the place of a part of a constant name.
 */
final class WildcardMaskNodeTest extends TestCase
{
    #[Test]
    public function aWildcardIsNoType(): void
    {
        self::assertInstanceOf(Node::class, new WildcardMaskNode());
        self::assertNotInstanceOf(TypeNode::class, new WildcardMaskNode());
    }

    #[Test]
    public function aWildcardIsWrittenAsAnAsterisk(): void
    {
        $node = new WildcardMaskNode();

        self::assertSame('*', $node->toString());
        self::assertSame('*', (string) $node);
        self::assertSame('*', WildcardMaskNode::CHAR);
    }
}
