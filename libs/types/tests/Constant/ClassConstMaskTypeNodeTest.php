<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Constant;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Constant\ClassConstMaskTypeNode;
use TypeLang\Type\Constant\MaskNode;
use TypeLang\Type\Name\TypeName;
use TypeLang\Type\Constant\MaskNode\WildcardMaskNode;
use TypeLang\Type\Tests\TestCase;

final class ClassConstMaskTypeNodeTest extends TestCase
{

    #[Test]
    public function aMaskOfNothingButAWildcardCarriesNoSegments(): void
    {
        $node = new ClassConstMaskTypeNode(
            TypeName::from('MyEnum'),
            new MaskNode([new WildcardMaskNode()]),
        );

        self::assertSame([], $node->mask->getSegments());
        self::assertSame('*', $node->mask->toString());
    }
}
