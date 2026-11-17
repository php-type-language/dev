<?php

declare(strict_types=1);

namespace TypeLang\Bench\Mapper\Instantiator;

use Doctrine\Instantiator\Instantiator as DoctrineInstantiator;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\RetryThreshold;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;
use TypeLang\Bench\Mapper\Instantiator\Stub\ExampleStub;

#[Revs(10000), Warmup(2), Iterations(10), RetryThreshold(3)]
#[BeforeMethods('prepare')]
final class WithoutConstructorBench
{
    /**
     * @var \ReflectionClass<ExampleStub>
     */
    private \ReflectionClass $reflection;

    private ExampleStub $prototype;

    private DoctrineInstantiator $doctrine;

    public function prepare(): void
    {
        $this->reflection = new \ReflectionClass(ExampleStub::class);
        $this->prototype = $this->reflection->newInstanceWithoutConstructor();
    }

    public function prepareDoctrine(): void
    {
        $this->doctrine = new DoctrineInstantiator();
    }

    #[Groups(['baseline'])]
    public function benchReflectionPerCall(): void
    {
        (new \ReflectionClass(ExampleStub::class))->newInstanceWithoutConstructor();
    }

    public function benchCachedReflection(): void
    {
        $this->reflection->newInstanceWithoutConstructor();
    }

    public function benchClonePrototype(): void
    {
        clone $this->prototype;
    }

    #[BeforeMethods(['prepare', 'prepareDoctrine'])]
    public function benchDoctrine(): void
    {
        $this->doctrine->instantiate(ExampleStub::class);
    }
}
