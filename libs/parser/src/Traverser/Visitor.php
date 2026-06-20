<?php

declare(strict_types=1);

namespace TypeLang\Parser\Traverser;

use TypeLang\Node\Node;

abstract class Visitor implements VisitorInterface
{
    public function before(): void
    {
        // NO-OP
    }

    public function enter(Node $node): ?Command
    {
        return null;
    }

    public function leave(Node $node): void
    {
        // NO-OP
    }

    public function after(): void
    {
        // NO-OP
    }
}
