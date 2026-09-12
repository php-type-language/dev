<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\TypeNode;
use TypeLang\Type\WildcardNode;

/**
 * Tests for the asterisk standing in the place of something left unsaid.
 */
final class WildcardNodeTest extends TestCase
{
    #[Test]
    public function aWildcardIsAType(): void
    {
        self::assertInstanceOf(TypeNode::class, new WildcardNode());
    }

    #[Test]
    public function aWildcardIsWrittenAsAnAsterisk(): void
    {
        $node = new WildcardNode();

        self::assertSame('*', $node->toString());
        self::assertSame('*', (string) $node);
        self::assertSame('*', WildcardNode::CHAR);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        self::assertSame(0, (new WildcardNode())->offset);
    }
}
