<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel\Telescope;

use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use TypeLang\Mapper\Profiler\MapperCall;
use TypeLang\Mapper\Profiler\ProfilerInterface;

/**
 * Records the calls of the mappers as log entries of Telescope, tagged with
 * the name of the mapper. A failed call is a warning with the violations, a
 * successful one is a debug entry. The input and the output of a call are
 * not recorded.
 */
final class MapperRecorder implements ProfilerInterface
{
    public const TAG = 'type-lang-mapper';

    public function startCall(string $mapper, string $method, ?string $type): void {}

    public function finishCall(MapperCall $call): void
    {
        if (!Telescope::isRecording()) {
            return;
        }

        $isFailed = $call->error !== null;
        $tags = [self::TAG, self::TAG . ':' . $call->mapper];

        if ($isFailed) {
            $tags[] = self::TAG . ':failed';
        }

        Telescope::recordLog(IncomingEntry::make([
            'level' => $isFailed ? 'warning' : 'debug',
            'message' => \sprintf(
                'Mapper "%s": %s(%s) %s in %.2f ms',
                $call->mapper,
                $call->method,
                $call->type ?? 'mixed',
                $isFailed ? 'failed' : 'succeeded',
                $call->duration,
            ),
            'context' => [
                'mapper' => $call->mapper,
                'method' => $call->method,
                'type' => $call->type,
                'duration' => $call->duration,
                'error' => $call->error === null ? null : $call->error::class . ': ' . $call->error->getMessage(),
                'violations' => self::getViolations($call),
                'called at' => $call->file === null ? null : $call->file . ':' . ($call->line ?? 0),
            ],
        ])->tags($tags));
    }

    public function recordType(string $mapper, string $type, string $class): void {}

    public function recordMetadataRead(string $mapper, string $class, float $duration): void {}

    /**
     * @return list<non-empty-string>
     */
    private static function getViolations(MapperCall $call): array
    {
        $result = [];

        foreach ($call->getViolations() as $violation) {
            $result[] = $violation['path'] . ': ' . $violation['message'];
        }

        return $result;
    }
}
