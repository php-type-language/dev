<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Diagnostics\Tests\Path;

use PHPUnit\Framework\Attributes\RequiresSetting;
use TypeLang\Mapper\Diagnostics\Path\Path;
use TypeLang\Mapper\Diagnostics\Path\PathStack;
use TypeLang\Mapper\Diagnostics\Path\Segment\ConditionalBranchSegment;
use TypeLang\Mapper\Diagnostics\Path\Segment\ConditionalBranchSegment\ConditionalBranch;
use TypeLang\Mapper\Diagnostics\Path\Segment\IntersectionLeafSegment;
use TypeLang\Mapper\Diagnostics\Path\Segment\KeySegment;
use TypeLang\Mapper\Diagnostics\Path\Segment\ObjectSegment;
use TypeLang\Mapper\Diagnostics\Path\Segment\PropertySegment;
use TypeLang\Mapper\Diagnostics\Path\Segment\UnionLeafSegment;
use TypeLang\Mapper\Diagnostics\Tests\Stub\UserStub;
use TypeLang\Mapper\Diagnostics\Tests\TestCase;

final class PathStackTest extends TestCase
{
    /**
     * @return list<int|string>
     */
    private static function readKeys(Path $path): array
    {
        $keys = [];

        foreach ($path->segments as $segment) {
            self::assertInstanceOf(KeySegment::class, $segment);
            $keys[] = $segment->key;
        }

        return $keys;
    }

    public function testNewStackIsTheRoot(): void
    {
        self::assertSame([], (new PathStack())->capture()->segments);
    }

    public function testArrayIndexesBecomeKeySegments(): void
    {
        $stack = new PathStack();

        $stack->enterIntoArrayIndex('order');
        $stack->enterIntoArrayIndex('items');
        $stack->enterIntoArrayIndex(0);

        self::assertSame(['order', 'items', 0], self::readKeys($stack->capture()));
    }

    public function testPropertyAndStringKeyWithTheSameNameDiffer(): void
    {
        $stack = new PathStack();

        $stack->enterIntoObject(UserStub::class);
        $stack->enterIntoObjectProperty('name');
        $stack->enterIntoArrayIndex('name');

        $segments = $stack->capture()->segments;

        self::assertCount(3, $segments);
        self::assertInstanceOf(PropertySegment::class, $segments[1]);
        self::assertSame('name', $segments[1]->property);
        self::assertInstanceOf(KeySegment::class, $segments[2]);
        self::assertSame('name', $segments[2]->key);
    }

    public function testLeaveRemovesTheLastSegment(): void
    {
        $stack = new PathStack();
        $stack->enterIntoArrayIndex('items');
        $stack->enterIntoArrayIndex(0);

        $stack->leave();

        self::assertSame(['items'], self::readKeys($stack->capture()));
    }

    public function testEnterAfterLeaveReusesTheSlot(): void
    {
        $stack = new PathStack();
        $stack->enterIntoArrayIndex('a');
        $stack->enterIntoArrayIndex('b');
        $stack->enterIntoArrayIndex('c');
        $stack->leave();
        $stack->leave();

        $stack->enterIntoArrayIndex('x');

        self::assertSame(['a', 'x'], self::readKeys($stack->capture()));
    }

    public function testCapturedPathDoesNotChangeWithTheStack(): void
    {
        $stack = new PathStack();
        $stack->enterIntoArrayIndex('items');
        $stack->enterIntoArrayIndex(0);

        $captured = $stack->capture();

        $stack->leave();
        $stack->enterIntoArrayIndex(1);
        $stack->enterIntoArrayIndex('name');

        self::assertSame(['items', 0], self::readKeys($captured));
    }

    public function testNamedStepsCreateSegmentsOfTheirKind(): void
    {
        $stack = new PathStack();

        $stack->enterIntoObject(UserStub::class);
        $stack->enterIntoObjectProperty('items');
        $stack->enterIntoArrayIndex(3);
        $stack->enterIntoUnionLeaf(1);
        $stack->enterIntoIntersectionLeaf(2);
        $stack->enterIntoConditionalBranch(ConditionalBranch::Else);

        $segments = $stack->capture()->segments;

        self::assertCount(6, $segments);
        self::assertInstanceOf(ObjectSegment::class, $segments[0]);
        self::assertSame(UserStub::class, $segments[0]->class);
        self::assertInstanceOf(PropertySegment::class, $segments[1]);
        self::assertSame('items', $segments[1]->property);
        self::assertInstanceOf(KeySegment::class, $segments[2]);
        self::assertSame(3, $segments[2]->key);
        self::assertInstanceOf(UnionLeafSegment::class, $segments[3]);
        self::assertSame(1, $segments[3]->index);
        self::assertInstanceOf(IntersectionLeafSegment::class, $segments[4]);
        self::assertSame(2, $segments[4]->index);
        self::assertInstanceOf(ConditionalBranchSegment::class, $segments[5]);
        self::assertSame(ConditionalBranch::Else, $segments[5]->branch);
    }

    #[RequiresSetting('zend.assertions', '1')]
    public function testLeaveOnEmptyStackFailsAssertion(): void
    {
        $this->expectException(\AssertionError::class);

        (new PathStack())->leave();
    }
}
