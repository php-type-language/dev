<?php

declare(strict_types=1);

namespace TypeLang\Type;

interface NodeInterface
{
    /**
     * Gets token offset defined in the source code.
     *
     * It is recommended to use the `phplrt/position` package to determine
     * the line and column from this information:
     *
     * ```php
     * $position = Phplrt\Position\Position::fromOffset(
     *     source: \file_get_contents($filename),
     *     offset: $node->offset,
     * );
     *
     * echo 'line: ' . $position->getLine() . "\n"
     *      'column: ' . $position->getColumn();
     * ```
     *
     * @var int<0, max>
     */
    public int $offset {
        get;
    }
}
