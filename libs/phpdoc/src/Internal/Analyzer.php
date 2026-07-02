<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Internal;

use TypeLang\PhpDoc\Internal\Splitter\SplitterInterface;

/**
 * Groups the significant segments of a DocBlock comment into sections (a
 * description followed by tags) and builds the {@see SourceMap} for them.
 *
 * It only slices and maps the comment: parsing the description and tag
 * contents is left to the caller.
 */
final readonly class Analyzer
{
    public function __construct(
        private SplitterInterface $splitter,
    ) {}

    public function analyze(string $docblock): RawDocBlock
    {
        $map = new SourceMap();

        $current = '';
        $blocks = [];

        foreach ($this->splitter->split($docblock) as $segment) {
            $text = $segment->text;
            $offset = $segment->offset;

            $map->addMapping($text, $offset);

            // A segment starting with "@" opens a new tag section, flushing
            // whatever was accumulated for the previous one.
            if (\str_starts_with($text, '@')) {
                $blocks[] = $current;
                $current = '';
            }

            $current .= $text;
        }

        $blocks[] = $current;

        // The first section is always the description; the rest are tags.
        $description = \array_shift($blocks);

        return new RawDocBlock($description, $blocks, $map);
    }
}
