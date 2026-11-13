<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Diagnostics\Path;

use TypeLang\Mapper\Diagnostics\Path\Segment\ConditionalBranchSegment;
use TypeLang\Mapper\Diagnostics\Path\Segment\ConditionalBranchSegment\ConditionalBranch;
use TypeLang\Mapper\Diagnostics\Path\Segment\IntersectionLeafSegment;
use TypeLang\Mapper\Diagnostics\Path\Segment\KeySegment;
use TypeLang\Mapper\Diagnostics\Path\Segment\ObjectSegment;
use TypeLang\Mapper\Diagnostics\Path\Segment\PropertySegment;
use TypeLang\Mapper\Diagnostics\Path\Segment\SegmentKind;
use TypeLang\Mapper\Diagnostics\Path\Segment\UnionLeafSegment;

/**
 * Path of the current position during a mapping run.
 *
 * A step stores only its kind and data, segments are created on
 * {@see self::capture()}.
 */
final class PathStack
{
    /**
     * Kind of each step
     *
     * @var array<int<0, max>, SegmentKind>
     */
    private array $kinds = [];

    /**
     * Key, class, property name or leaf index of each step, absent for
     * a branch of a conditional type
     *
     * @var array<int<0, max>, int|string|ConditionalBranch>
     */
    private array $values = [];

    /**
     * @var int<0, max>
     */
    private int $depth = 0;

    /**
     * Appends a key of an array or an index of a list.
     */
    public function enterIntoArrayIndex(int|string $index): void
    {
        $this->kinds[$this->depth] = SegmentKind::ArrayIndex;
        $this->values[$this->depth++] = $index;
    }

    /**
     * Appends an object of a class, properties entered after it belong to
     * this class.
     *
     * @param class-string $class
     */
    public function enterIntoObject(string $class): void
    {
        $this->kinds[$this->depth] = SegmentKind::Object;
        $this->values[$this->depth++] = $class;
    }

    /**
     * Appends a property of the object entered last.
     *
     * @param non-empty-string $property
     */
    public function enterIntoObjectProperty(string $property): void
    {
        $this->kinds[$this->depth] = SegmentKind::ObjectProperty;
        $this->values[$this->depth++] = $property;
    }

    /**
     * Appends a branch of a union type.
     *
     * @param int<0, max> $index
     */
    public function enterIntoUnionLeaf(int $index): void
    {
        $this->kinds[$this->depth] = SegmentKind::UnionLeaf;
        $this->values[$this->depth++] = $index;
    }

    /**
     * Appends a member of an intersection type.
     *
     * @param int<0, max> $index
     */
    public function enterIntoIntersectionLeaf(int $index): void
    {
        $this->kinds[$this->depth] = SegmentKind::IntersectionLeaf;
        $this->values[$this->depth++] = $index;
    }

    /**
     * Appends a branch of a conditional type.
     */
    public function enterIntoConditionalBranch(ConditionalBranch $branch): void
    {
        $this->kinds[$this->depth] = SegmentKind::Condition;
        $this->values[$this->depth++] = $branch;
    }

    public function leave(): void
    {
        \assert($this->depth > 0, 'Path stack is empty');

        --$this->depth;
    }

    /**
     * Returns a copy of the current path.
     */
    public function capture(): Path
    {
        $segments = [];

        for ($i = 0; $i < $this->depth; ++$i) {
            $segments[] = match ($this->kinds[$i]) {
                SegmentKind::ArrayIndex => new KeySegment($this->values[$i]),
                // @phpstan-ignore-next-line argument.type (data of the kind is written by enterInto*())
                SegmentKind::Object => new ObjectSegment($this->values[$i]),
                // @phpstan-ignore-next-line argument.type (data of the kind is written by enterInto*())
                SegmentKind::ObjectProperty => new PropertySegment($this->values[$i]),
                // @phpstan-ignore-next-line argument.type (data of the kind is written by enterInto*())
                SegmentKind::UnionLeaf => new UnionLeafSegment($this->values[$i]),
                // @phpstan-ignore-next-line argument.type (data of the kind is written by enterInto*())
                SegmentKind::IntersectionLeaf => new IntersectionLeafSegment($this->values[$i]),
                // @phpstan-ignore-next-line argument.type (data of the kind is written by enterInto*())
                SegmentKind::Condition => new ConditionalBranchSegment($this->values[$i]),
            };
        }

        return new Path($segments);
    }
}
