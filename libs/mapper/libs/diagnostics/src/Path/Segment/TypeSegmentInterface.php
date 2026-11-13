<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Diagnostics\Path\Segment;

/**
 * Step into a part of the type the input is mapped by, such as a branch of
 * a union, with no step in the input itself.
 */
interface TypeSegmentInterface extends SegmentInterface {}
