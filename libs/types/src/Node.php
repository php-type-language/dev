<?php

declare(strict_types=1);

namespace TypeLang\Type;

abstract class Node implements NodeInterface
{
    /**
     * An alias of {@see offset()} method.
     *
     * @var int<0, max>
     */
    public int $offset = 0;

    public function offset(): int
    {
        return $this->offset;
    }
}
