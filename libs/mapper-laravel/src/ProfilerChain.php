<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel;

use TypeLang\Mapper\Profiler\MapperCall;
use TypeLang\Mapper\Profiler\ProfilerInterface;

/**
 * Passes the trace of the mappers to each of the profilers.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal TypeLang\Mapper\Bridge\Laravel
 */
final class ProfilerChain implements ProfilerInterface
{
    public function __construct(
        /**
         * @var non-empty-list<ProfilerInterface>
         */
        private readonly array $profilers,
    ) {}

    public function startCall(string $mapper, string $method, ?string $type): void
    {
        foreach ($this->profilers as $profiler) {
            $profiler->startCall($mapper, $method, $type);
        }
    }

    public function finishCall(MapperCall $call): void
    {
        foreach ($this->profilers as $profiler) {
            $profiler->finishCall($call);
        }
    }

    public function recordType(string $mapper, string $type, string $class): void
    {
        foreach ($this->profilers as $profiler) {
            $profiler->recordType($mapper, $type, $class);
        }
    }

    public function recordMetadataRead(string $mapper, string $class, float $duration): void
    {
        foreach ($this->profilers as $profiler) {
            $profiler->recordMetadataRead($mapper, $class, $duration);
        }
    }
}
