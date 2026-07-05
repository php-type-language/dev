<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Condition;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Condition\EqualConditionNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Tests\TestCase;

final class EqualConditionNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresSubjectAndTarget(): void
    {
        $subject = new NamedTypeNode(Name::createFromString('T'));
        $target = new NamedTypeNode(Name::createFromString('string'));
        $node = new EqualConditionNode($subject, $target);

        self::assertSame($subject, $node->subject);
        self::assertSame($target, $node->target);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new EqualConditionNode(
            new NamedTypeNode(Name::createFromString('T')),
            new NamedTypeNode(Name::createFromString('string')),
        );

        self::assertSame(0, $node->offset);
    }
}
