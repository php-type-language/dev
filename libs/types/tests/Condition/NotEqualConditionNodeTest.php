<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Condition;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Condition\NotEqualConditionNode;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Tests\TestCase;

final class NotEqualConditionNodeTest extends TestCase
{
    #[Test]
    public function constructorStoresSubjectAndTarget(): void
    {
        $subject = new NamedTypeNode(Name::createFromString('T'));
        $target = new NamedTypeNode(Name::createFromString('null'));
        $node = new NotEqualConditionNode($subject, $target);

        $this->assertSame($subject, $node->subject);
        $this->assertSame($target, $node->target);
    }

    #[Test]
    public function defaultOffsetIsZero(): void
    {
        $node = new NotEqualConditionNode(
            new NamedTypeNode(Name::createFromString('T')),
            new NamedTypeNode(Name::createFromString('null')),
        );

        $this->assertSame(0, $node->offset);
    }
}
