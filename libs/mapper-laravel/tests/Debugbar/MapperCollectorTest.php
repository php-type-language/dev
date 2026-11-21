<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel\Tests\Debugbar;

use DebugBar\DataCollector\TimeDataCollector;
use TypeLang\Mapper\Bridge\Laravel\Debugbar\MapperCollector;
use TypeLang\Mapper\Bridge\Laravel\Tests\Stub\UserStub;
use TypeLang\Mapper\Bridge\Laravel\Tests\TestCase;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MetadataBuilder;
use TypeLang\Mapper\Profiler\TraceableMapper;
use TypeLang\Mapper\Profiler\TraceableMetadataProvider;
use TypeLang\Mapper\Profiler\TypeBuildRecorder;

final class MapperCollectorTest extends TestCase
{
    public function testCallsAreCollectedByTheirLabels(): void
    {
        $collector = new MapperCollector();
        $mapper = new TraceableMapper(new Mapper(), 'api', $collector);

        $mapper->denormalize('42', 'int');

        try {
            $mapper->denormalize(['old'], 'list<int>');
            self::fail('The value is denormalized');
        } catch (InvalidValueException) {
        }

        $data = $collector->collect();
        $labels = \array_keys($data['calls']);

        self::assertSame(2, $data['count']);
        self::assertMatchesRegularExpression('/^#1 api: denormalize\(int\) [\d.]+ ms$/', $labels[0]);
        self::assertMatchesRegularExpression('/^#2 api: denormalize\(list<int>\) [\d.]+ ms failed$/', $labels[1]);
    }

    public function testTypesAndMetadataReadsAreCollected(): void
    {
        $collector = new MapperCollector();
        $mapper = new Mapper(
            decorators: [new TypeBuildRecorder('api', $collector)],
            metadata: new TraceableMetadataProvider(MetadataBuilder::createDefault()->build(), 'api', $collector),
        );

        $mapper->denormalize(['userName' => 'root', 'age' => 42], UserStub::class);

        $data = $collector->collect();

        self::assertArrayHasKey('Built types', $data['calls']);
        self::assertArrayHasKey('Metadata reads', $data['calls']);
    }

    public function testNestedCallsAreMeasuredOnTimeline(): void
    {
        $timeline = new TimeDataCollector();
        $collector = new MapperCollector($timeline);

        $collector->startCall('default', 'denormalize', 'int');
        $collector->startCall('default', 'normalize', null);

        self::assertTrue($timeline->hasStartedMeasure(MapperCollector::NAME . '.2'));

        (new TraceableMapper(new Mapper(), 'default', $collector))->isNormalizable(1, 'int');

        $labels = \array_column($timeline->getMeasures(), 'label');

        self::assertSame(['default: isNormalizable(int)'], $labels);
        self::assertTrue($timeline->hasStartedMeasure(MapperCollector::NAME . '.1'));
        self::assertTrue($timeline->hasStartedMeasure(MapperCollector::NAME . '.2'));
    }

    public function testWidgetShowsCallsWithBadge(): void
    {
        $widgets = (new MapperCollector())->getWidgets();

        self::assertSame(MapperCollector::NAME . '.calls', $widgets['mapper']['map']);
        self::assertSame(MapperCollector::NAME . '.count', $widgets['mapper:badge']['map']);
    }
}
