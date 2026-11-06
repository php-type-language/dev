<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Template;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Template\TemplateArgumentNode;
use TypeLang\Type\Template\WildcardTemplateArgumentNode;
use TypeLang\Type\Tests\TestCase;
use TypeLang\Type\TypeNode;

/**
 * Tests for the asterisk standing in the place of a template argument.
 */
final class WildcardTemplateArgumentNodeTest extends TestCase
{
    #[Test]
    public function aWildcardIsAnArgumentAndNoType(): void
    {
        $node = new WildcardTemplateArgumentNode();

        self::assertInstanceOf(TemplateArgumentNode::class, $node);
        self::assertNotInstanceOf(TypeNode::class, $node);
    }

    #[Test]
    public function aWildcardCarriesNoVariance(): void
    {
        self::assertFalse(\property_exists(new WildcardTemplateArgumentNode(), 'variance'));
    }
}
