<?php

declare(strict_types=1);

namespace TypeLang\Type;

interface NodeInterface
{
    /**
     * Gets token offset defined in the source code.
     *
     * @return int<0, max>
     */
    public function getOffset(): int;
}
