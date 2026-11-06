<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Callable;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Callable\CallableParameterNode;
use TypeLang\Type\Name\TypeName;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Tests\TestCase;

final class CallableParameterNodeTest extends TestCase
{

    #[Test]
    public function throwsWhenBothTypeAndNameAreNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CallableParameterNode();
    }

    #[Test]
    public function throwsWhenBothVariadicAndOptionalAreTrue(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CallableParameterNode(
            type: new NamedTypeNode(TypeName::from('int')),
            isVariadic: true,
            isOptional: true,
        );
    }
}
