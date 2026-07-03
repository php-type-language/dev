<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Parser\Grammar;

/**
 * A reading position over a tag suffix that grammar rules consume from.
 */
final class Cursor
{
    /**
     * @var int<0, max>
     */
    private int $position = 0;

    /**
     * @var int<0, max>
     */
    private int $furthest = 0;

    /**
     * @var int<0, max>
     */
    private readonly int $length;

    public function __construct(
        private readonly string $buffer,
        /**
         * Byte offset of the buffer inside the source.
         *
         * @var int<0, max>
         */
        public readonly int $base = 0,
    ) {
        $this->length = \strlen($buffer);
    }

    /**
     * The current byte offset within the source.
     *
     * @var int<0, max>
     */
    public int $offset {
        get => $this->base + $this->position;
    }

    /**
     * The byte offset to report when a match fails.
     *
     * @var int<0, max>
     */
    public int $furthestOffset {
        get => $this->base + $this->furthest;
    }

    public bool $isEof {
        get => $this->position >= $this->length;
    }

    /**
     * Returns the current position for later {@see seek()}.
     *
     * @return int<0, max>
     */
    public function tell(): int
    {
        return $this->position;
    }

    /**
     * Restores a position previously obtained from {@see tell()}.
     *
     * @param int<0, max> $position
     */
    public function seek(int $position): void
    {
        $this->position = $position;
    }

    /**
     * Advances over any leading whitespace.
     */
    public function skipWhitespace(): void
    {
        $this->position += \strspn($this->buffer, " \t\r\n", $this->position);
        $this->advanceHighWaterMark();
    }

    /**
     * Reads the next whitespace-delimited word, or an empty string when none
     * is left.
     */
    public function readWord(): string
    {
        $length = \strcspn($this->buffer, " \t\r\n", $this->position);
        $word = \substr($this->buffer, $this->position, $length);
        $this->position += $length;
        $this->advanceHighWaterMark();

        return $word;
    }

    /**
     * Reads everything that is left.
     */
    public function readRemainder(): string
    {
        $rest = \substr($this->buffer, $this->position);
        $this->position = $this->length;
        $this->advanceHighWaterMark();

        return $rest;
    }

    private function advanceHighWaterMark(): void
    {
        if ($this->position > $this->furthest) {
            $this->furthest = $this->position;
        }
    }
}
