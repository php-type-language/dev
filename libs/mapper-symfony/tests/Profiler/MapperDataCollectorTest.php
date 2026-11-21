<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Symfony\Tests\Profiler;

use Symfony\Component\Stopwatch\Stopwatch;
use TypeLang\Mapper\Bridge\Symfony\Profiler\MapperDataCollector;
use TypeLang\Mapper\Bridge\Symfony\Tests\TestCase;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Profiler\TraceableMapper;

final class MapperDataCollectorTest extends TestCase
{
    public function testCallIsCollected(): void
    {
        $collector = new MapperDataCollector();
        $mapper = new TraceableMapper(new Mapper(), 'api', $collector);

        $line = __LINE__ + 1;
        self::assertSame(42, $mapper->denormalize('42', 'int'));

        $collector->lateCollect();
        $call = $collector->getCalls()[0];

        self::assertSame('api', $call['mapper']);
        self::assertSame('denormalize', $call['method']);
        self::assertSame('int', $call['type']);
        self::assertNull($call['error']);
        self::assertSame(__FILE__, $call['file']);
        self::assertSame($line, $call['line']);
        self::assertSame(0, $collector->getErrorCount());
    }

    public function testFailedCallIsCollectedWithViolations(): void
    {
        $collector = new MapperDataCollector();
        $mapper = new TraceableMapper(new Mapper(), 'default', $collector);

        try {
            $mapper->denormalize(['old'], 'list<int>');
            self::fail('The value is denormalized');
        } catch (InvalidValueException) {
        }

        $collector->lateCollect();
        $call = $collector->getCalls()[0];

        self::assertSame(InvalidValueException::class, $call['error']['class'] ?? null);
        self::assertSame('$[0]', $call['violations'][0]['path']);
        self::assertSame('Value must be of type int, but "old" given', $call['violations'][0]['message']);
        self::assertSame(1, $collector->getErrorCount());
    }

    public function testCallIsReportedToStopwatch(): void
    {
        $stopwatch = new Stopwatch();
        $mapper = new TraceableMapper(new Mapper(), 'default', new MapperDataCollector($stopwatch));

        $mapper->normalize(42, 'int');

        self::assertFalse($stopwatch->isStarted('normalize(int)'));
        self::assertSame('type_lang_mapper', $stopwatch->getEvent('normalize(int)')->getCategory());
    }

    public function testResetForgetsCalls(): void
    {
        $collector = new MapperDataCollector();
        $mapper = new TraceableMapper(new Mapper(), 'default', $collector);

        $mapper->isNormalizable(42, 'int');
        $collector->reset();
        $collector->lateCollect();

        self::assertSame([], $collector->getCalls());
    }
}
