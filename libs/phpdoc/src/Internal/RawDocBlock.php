<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Internal;

/**
 * A raw, unparsed subdivision of a DocBlock comment
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\PhpDoc
 */
final readonly class RawDocBlock
{
    /**
     * @param string $description raw description section (the text preceding
     *        the first tag; may be an empty string)
     * @param list<string> $tags raw tag sections, each starting with an "@" tag
     * @param SourceMap $map mapping from concatenated section offsets back to
     *        offsets in the original DocBlock
     */
    public function __construct(
        public string $description,
        public array $tags,
        public SourceMap $map,
    ) {}
}
