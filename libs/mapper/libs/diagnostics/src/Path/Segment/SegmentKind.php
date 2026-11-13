<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Diagnostics\Path\Segment;

enum SegmentKind
{
    case ArrayIndex;
    case Object;
    case ObjectProperty;
    case UnionLeaf;
    case IntersectionLeaf;
    case Condition;
}
