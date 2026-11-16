<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Diagnostics\Path\PathStack;

/**
 * Kind of a step of {@see \TypeLang\Mapper\Diagnostics\Path\PathStack}, the
 * segment class the step becomes on capture.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Diagnostics\Path
 */
enum SegmentKind
{
    case Key;
    case Object;
    case Property;
    case UnionLeaf;
    case IntersectionLeaf;
    case ConditionalBranch;
}
