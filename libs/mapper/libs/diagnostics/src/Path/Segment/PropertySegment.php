<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Diagnostics\Path\Segment;

/**
 * Step into a property of an object of a known class.
 */
final class PropertySegment implements DataSegmentInterface
{
    public function __construct(
        /**
         * Class declaring the property
         *
         * @var class-string
         */
        public readonly string $class,
        /**
         * @var non-empty-string
         */
        public readonly string $property,
    ) {}
}
