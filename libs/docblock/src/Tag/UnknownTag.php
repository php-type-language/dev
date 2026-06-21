<?php

declare(strict_types=1);

namespace TypeLang\DocBlock\Tag;

final class UnknownTag extends Tag implements InvalidTagInterface
{
    /**
     * @var non-empty-string
     */
    public const string DEFAULT_UNKNOWN_TAG_NAME = 'unknown';

    public function __construct(
        public readonly \Throwable $reason,
        \Stringable|string|null $description = null,
    ) {
        parent::__construct(self::DEFAULT_UNKNOWN_TAG_NAME, $description);
    }

    public function __toString(): string
    {
        return \sprintf('@%s', $this->description);
    }
}
