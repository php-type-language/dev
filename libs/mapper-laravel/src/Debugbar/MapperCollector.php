<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel\Debugbar;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use DebugBar\DataCollector\TimeDataCollector;
use TypeLang\Mapper\Profiler\MapperCall;
use TypeLang\Mapper\Profiler\ProfilerInterface;

/**
 * Collects the calls of the mappers, the types they build and the metadata
 * they read for the mapper tab of the Debugbar, the calls are also shown on
 * the timeline.
 */
final class MapperCollector extends DataCollector implements
    Renderable,
    ProfilerInterface
{
    public const NAME = 'type_lang_mapper';

    /**
     * @var list<MapperCall>
     */
    private array $calls = [];

    /**
     * Built types by the mapper
     *
     * @var array<non-empty-string, list<string>>
     */
    private array $types = [];

    /**
     * Number of metadata reads by the mapper and the class
     *
     * @var array<non-empty-string, array<class-string, int<1, max>>>
     */
    private array $metadata = [];

    /**
     * Names of the started measures of the timeline, the last one is the
     * innermost call
     *
     * @var list<non-empty-string>
     */
    private array $measures = [];

    private int $nextMeasure = 0;

    public function __construct(
        private readonly ?TimeDataCollector $timeline = null,
    ) {}

    public function startCall(string $mapper, string $method, ?string $type): void
    {
        if ($this->timeline === null) {
            return;
        }

        $measure = self::NAME . '.' . ++$this->nextMeasure;
        $this->measures[] = $measure;

        $this->timeline->startMeasure($measure, \sprintf('%s: %s(%s)', $mapper, $method, $type ?? 'mixed'));
    }

    public function finishCall(MapperCall $call): void
    {
        $this->calls[] = $call;

        $measure = \array_pop($this->measures);

        if ($measure !== null && $this->timeline?->hasStartedMeasure($measure) === true) {
            $this->timeline->stopMeasure($measure);
        }
    }

    public function recordType(string $mapper, string $type, string $class): void
    {
        $this->types[$mapper][] = \sprintf('%s: %s', $type, $class);
    }

    public function recordMetadataRead(string $mapper, string $class, float $duration): void
    {
        $this->metadata[$mapper][$class] = ($this->metadata[$mapper][$class] ?? 0) + 1;
    }

    /**
     * @return array{count: int<0, max>, calls: array<non-empty-string, mixed>}
     */
    public function collect(): array
    {
        $calls = [];

        foreach ($this->calls as $index => $call) {
            $label = \sprintf(
                '#%d %s: %s(%s) %.2f ms%s',
                $index + 1,
                $call->mapper,
                $call->method,
                $call->type ?? 'mixed',
                $call->duration,
                $call->error === null ? '' : ' failed',
            );

            $calls[$label] = $this->formatValue([
                'input' => $call->input,
                'output' => $call->output,
                'error' => $call->error === null ? null : $call->error::class . ': ' . $call->error->getMessage(),
                'violations' => $call->getViolations(),
                'called at' => $call->file === null ? null : $call->file . ':' . ($call->line ?? 0),
            ]);
        }

        if ($this->types !== []) {
            $calls['Built types'] = $this->formatValue($this->types);
        }

        if ($this->metadata !== []) {
            $calls['Metadata reads'] = $this->formatValue($this->metadata);
        }

        return ['count' => \count($this->calls), 'calls' => $calls];
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @return array<non-empty-string, array<non-empty-string, mixed>>
     */
    public function getWidgets(): array
    {
        $widget = 'PhpDebugBar.Widgets.VariableListWidget';

        if ($this->isJsonDumperUsed()) {
            $widget = 'PhpDebugBar.Widgets.JsonVariableListWidget';
        }

        if ($this->isHtmlDumperUsed()) {
            $widget = 'PhpDebugBar.Widgets.HtmlVariableListWidget';
        }

        return [
            'mapper' => [
                'icon' => 'exchange',
                'widget' => $widget,
                'map' => self::NAME . '.calls',
                'default' => '{}',
            ],
            'mapper:badge' => [
                'map' => self::NAME . '.count',
                'default' => 0,
            ],
        ];
    }

    private function formatValue(mixed $value): mixed
    {
        return $this->getDataFormatter()->formatVar($value);
    }

    /**
     * Whether the data formatter dumps the values to JSON, php-debugbar 2
     * formats them to text.
     */
    private function isJsonDumperUsed(): bool
    {
        // @phpstan-ignore-next-line the method depends on the installed version
        return \method_exists($this, 'isJsonVarDumperUsed') && $this->isJsonVarDumperUsed() === true;
    }

    /**
     * Whether the data formatter dumps the values to HTML, php-debugbar 2
     * formats them to text.
     */
    private function isHtmlDumperUsed(): bool
    {
        // @phpstan-ignore-next-line the method depends on the installed version
        return \method_exists($this, 'isJsonVarDumperUsed') && $this->isHtmlVarDumperUsed() === true;
    }
}
