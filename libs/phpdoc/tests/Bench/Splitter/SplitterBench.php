<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\Bench\Splitter;

use TypeLang\PhpDoc\Internal\Splitter\SplitterInterface;

abstract class SplitterBench
{
    private const string LITTLE_SAMPLE = <<<'DOC'
        /**
         * Cross product of every parser with line-ending fixtures. Each fixture
         * declares the EXACT segments (verbatim text including the trailing line
         * terminator, plus its byte offset) the parser is expected to produce.
         *
         * @return iterable<string, array{CommentParserInterface, string, list<array{string, int<0, max>}>}>
         */
        DOC;

    private const string BIG_SAMPLE = <<<'DOC'
        /**
         * Returns significant parts of the DocBlock comment with their offsets of
         * the returned section, relative to the beginning.
         *
         * ```
         * $result = $reader->read(<<<'DOC'
         *      /**
         *       * Example line 1
         *       *
         *       * @​tag1 type Description of tag1
         *       *​/
         *      DOC);
         *
         * // The $result contains:
         * // object(Segment) { offset: 7, text: 'Example line 1' }
         * // object(Segment) { offset: 28, text: '@tag1 type Description of tag1' }
         * ```
         *
         * Returns significant parts of the DocBlock comment with their offsets of
         * the returned section, relative to the beginning.
         *
         * ```
         * $result = $reader->read(<<<'DOC'
         *      /**
         *       * Example line 1
         *       *
         *       * @​tag1 type Description of tag1
         *       *​/
         *      DOC);
         *
         * // The $result contains:
         * // object(Segment) { offset: 7, text: 'Example line 1' }
         * // object(Segment) { offset: 28, text: '@tag1 type Description of tag1' }
         * ```
         *
         * Returns significant parts of the DocBlock comment with their offsets of
         * the returned section, relative to the beginning.
         *
         * ```
         * $result = $reader->read(<<<'DOC'
         *      /**
         *       * Example line 1
         *       *
         *       * @​tag1 type Description of tag1
         *       *​/
         *      DOC);
         *
         * // The $result contains:
         * // object(Segment) { offset: 7, text: 'Example line 1' }
         * // object(Segment) { offset: 28, text: '@tag1 type Description of tag1' }
         * ```
         *
         * Returns significant parts of the DocBlock comment with their offsets of
         * the returned section, relative to the beginning.
         *
         * ```
         * $result = $reader->read(<<<'DOC'
         *      /**
         *       * Example line 1
         *       *
         *       * @​tag1 type Description of tag1
         *       *​/
         *      DOC);
         *
         * // The $result contains:
         * // object(Segment) { offset: 7, text: 'Example line 1' }
         * // object(Segment) { offset: 28, text: '@tag1 type Description of tag1' }
         * ```
         *
         * Returns significant parts of the DocBlock comment with their offsets of
         * the returned section, relative to the beginning.
         *
         * ```
         * $result = $reader->read(<<<'DOC'
         *      /**
         *       * Example line 1
         *       *
         *       * @​tag1 type Description of tag1
         *       *​/
         *      DOC);
         *
         * // The $result contains:
         * // object(Segment) { offset: 7, text: 'Example line 1' }
         * // object(Segment) { offset: 28, text: '@tag1 type Description of tag1' }
         * ```
         *
         * @return iterable<array-key, Segment>
         */
        DOC;


    abstract protected SplitterInterface $splitter {
        get;
    }

    public function benchSplitLittleDocBlock(): void
    {
        foreach ($this->splitter->split(self::LITTLE_SAMPLE) as $segment) {
            // NO-OP
        }
    }

    public function benchSplitBigDocBlock(): void
    {
        foreach ($this->splitter->split(self::BIG_SAMPLE) as $segment) {
            // NO-OP
        }
    }
}
