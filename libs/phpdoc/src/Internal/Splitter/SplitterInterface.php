<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Internal\Splitter;

/**
 * This interface is responsible for reading significant
 * sections of the DocBlock comment.
 */
interface SplitterInterface
{
    /**
     * Returns significant parts of the DocBlock comment with their offsets of
     * the returned section, relative to the beginning.
     *
     * ```php
     * $result = $parser->parse(<<<'DOC'
     *      /**
     *       * Example line 1
     *       *
     *       * @tag1 type Description of tag1
     *       *∕
     *      DOC);
     *
     * // The $result contains:
     * // - Segment{ offset: 7, text: 'Example line 1' }
     * // - Segment{ offset: 28, text: '@tag1 type Description of tag1' }
     * ```
     *
     * @return iterable<array-key, Segment>
     */
    public function split(string $docblock): iterable;
}
