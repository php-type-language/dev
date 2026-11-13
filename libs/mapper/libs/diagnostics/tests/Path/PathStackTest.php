<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Diagnostics\Tests\Path;

use PHPUnit\Framework\Attributes\RequiresSetting;
use TypeLang\Mapper\Diagnostics\Path\Path;
use TypeLang\Mapper\Diagnostics\Path\PathStack;
use TypeLang\Mapper\Diagnostics\Path\Segment\ConditionalBranchSegment;
use TypeLang\Mapper\Diagnostics\Path\Segment\ConditionalBranchSegment\ConditionalBranch;
use TypeLang\Mapper\Diagnostics\Path\Segment\KeySegment;
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

    public function testCaptureTurnsRawKeysIntoKeySegments(): void
    {
        $stack = new PathStack();

        $stack->enter('order');
        $stack->enter('items');
        $stack->enter(0);

        self::assertSame(['order', 'items', 0], self::readKeys($stack->capture()));
    }

    public function testCaptureKeepsTheGivenSegmentInstance(): void
    {
        $property = new PropertySegment(UserStub::class, 'name');
        $key = new KeySegment('name');
        $stack = new PathStack();

        $stack->enter($property);
        $stack->enter($key);

        $segments = $stack->capture()->segments;

        self::assertCount(2, $segments);
        self::assertSame($property, $segments[0]);
        self::assertSame($key, $segments[1]);
    }

    public function testCaptureKeepsTheGivenTypeSegmentInstance(): void
    {
        $union = new UnionLeafSegment(1);
        $branch = new ConditionalBranchSegment(ConditionalBranch::Then);
        $stack = new PathStack();

        $stack->enter('items');
        $stack->enter($union);
        $stack->enter($branch);

        $segments = $stack->capture()->segments;

        self::assertCount(3, $segments);
        self::assertSame($union, $segments[1]);
        self::assertSame($branch, $segments[2]);
    }

    public function testPropertyAndStringKeyWithTheSameNameDiffer(): void
    {
        $stack = new PathStack();

        $stack->enter(new PropertySegment(UserStub::class, 'name'));
        $stack->enter('name');

        $segments = $stack->capture()->segments;

        self::assertCount(2, $segments);
        self::assertInstanceOf(PropertySegment::class, $segments[0]);
        self::assertSame('name', $segments[0]->property);
        self::assertSame(UserStub::class, $segments[0]->class);
        self::assertInstanceOf(KeySegment::class, $segments[1]);
        self::assertSame('name', $segments[1]->key);
    }

    public function testLeaveRemovesTheLastSegment(): void
    {
        $stack = new PathStack();
        $stack->enter('items');
        $stack->enter(0);

        $stack->leave();

        self::assertSame(['items'], self::readKeys($stack->capture()));
    }

    public function testEnterAfterLeaveReusesTheSlot(): void
    {
        $stack = new PathStack();
        $stack->enter('a');
        $stack->enter('b');
        $stack->enter('c');
        $stack->leave();
        $stack->leave();

        $stack->enter('x');

        self::assertSame(['a', 'x'], self::readKeys($stack->capture()));
    }

    public function testCapturedPathDoesNotChangeWithTheStack(): void
    {
        $stack = new PathStack();
        $stack->enter('items');
        $stack->enter(0);

        $captured = $stack->capture();

        $stack->leave();
        $stack->enter(1);
        $stack->enter('name');

        self::assertSame(['items', 0], self::readKeys($captured));
    }

    #[RequiresSetting('zend.assertions', '1')]
    public function testLeaveOnEmptyStackFailsAssertion(): void
    {
        $this->expectException(\AssertionError::class);

        (new PathStack())->leave();
    }
}
