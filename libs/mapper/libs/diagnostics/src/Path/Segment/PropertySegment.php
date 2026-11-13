<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Diagnostics\Path\Segment;

/**
 * Step into a property of the object of the preceding {@see ObjectSegment}.
 */
final class PropertySegment implements SegmentInterface
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public readonly string $property,
    ) {}
}
