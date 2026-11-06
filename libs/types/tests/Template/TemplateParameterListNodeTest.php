<?php

declare(strict_types=1);

namespace TypeLang\Type\Tests\Template;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\Type\Identifier;
use TypeLang\Type\Template\TemplateParameterListNode;
use TypeLang\Type\Template\TemplateParameterNode;
use TypeLang\Type\Tests\TestCase;

final class TemplateParameterListNodeTest extends TestCase
{
    #[Test]
    public function emptyListIsNotAllowed(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TemplateParameterListNode([]);
    }

    #[Test]
    public function lastParameterCannotBeRemoved(): void
    {
        $list = new TemplateParameterListNode([new TemplateParameterNode(new Identifier('T'))]);

        $this->expectException(\UnderflowException::class);

        unset($list[0]);
    }
}
