<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel\Tests\Telescope;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use TypeLang\Mapper\Bridge\Laravel\Telescope\MapperRecorder;
use TypeLang\Mapper\Bridge\Laravel\Tests\TestCase;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Profiler\TraceableMapper;

final class MapperRecorderTest extends TestCase
{
    protected function setUp(): void
    {
        $container = new Container();
        $container->instance('events', new Dispatcher($container));

        Container::setInstance($container);
        Telescope::$entriesQueue = [];
        Telescope::$shouldRecord = true;
    }

    protected function tearDown(): void
    {
        Telescope::$entriesQueue = [];
        Telescope::$shouldRecord = false;
        Container::setInstance(null);
    }

    public function testSucceededCallIsDebugEntry(): void
    {
        $mapper = new TraceableMapper(new Mapper(), 'api', new MapperRecorder());

        $line = __LINE__ + 1;
        $mapper->denormalize('42', 'int');

        $entry = self::getSingleEntry();

        self::assertSame('log', $entry->type);
        self::assertSame(['type-lang-mapper', 'type-lang-mapper:api'], $entry->tags);
        self::assertSame('debug', $entry->content['level']);
        self::assertMatchesRegularExpression(
            '/^Mapper "api": denormalize\(int\) succeeded in \d+\.\d{2} ms$/',
            $entry->content['message'],
        );
        self::assertSame(__FILE__ . ':' . $line, $entry->content['context']['called at']);
        self::assertArrayNotHasKey('input', $entry->content['context']);
    }

    public function testFailedCallIsWarningWithViolations(): void
    {
        $mapper = new TraceableMapper(new Mapper(), 'api', new MapperRecorder());

        try {
            $mapper->denormalize(['id' => 'x'], 'array{id: int}');
        } catch (InvalidValueException) {
        }

        $entry = self::getSingleEntry();

        self::assertSame(['type-lang-mapper', 'type-lang-mapper:api', 'type-lang-mapper:failed'], $entry->tags);
        self::assertSame('warning', $entry->content['level']);
        self::assertStringContainsString('failed', $entry->content['message']);
        self::assertSame(['$.id: Value must be of type int, but "x" given'], $entry->content['context']['violations']);
    }

    public function testCallIsNotRecordedWhileTelescopeIsPaused(): void
    {
        Telescope::$shouldRecord = false;

        (new TraceableMapper(new Mapper(), 'api', new MapperRecorder()))->denormalize('42', 'int');

        self::assertSame([], Telescope::$entriesQueue);
    }

    private static function getSingleEntry(): IncomingEntry
    {
        self::assertCount(1, Telescope::$entriesQueue);

        $result = Telescope::$entriesQueue[0];

        self::assertInstanceOf(IncomingEntry::class, $result);

        return $result;
    }
}
