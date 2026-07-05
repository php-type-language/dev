<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Condition;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Condition\LessThanOrEqualConditionNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Tests\TestCase;

final class LessThanOrEqualConditionNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresSubjectAndTarget(): void
    {
        $subject = new NamedTypeNode(Name::createFromString('T'));
        $target = new NamedTypeNode(Name::createFromString('int'));
        $node = new LessThanOrEqualConditionNode($subject, $target);

        self::assertSame($subject, $node->subject);
        self::assertSame($target, $node->target);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new LessThanOrEqualConditionNode(
            new NamedTypeNode(Name::createFromString('T')),
            new NamedTypeNode(Name::createFromString('int')),
        );

        self::assertSame(0, $node->offset);
    }
}
