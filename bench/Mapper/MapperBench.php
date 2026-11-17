<?php

declare(strict_types=1);

namespace TypeLang\Bench\Mapper;

use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\ParamProviders;
use PhpBench\Attributes\RetryThreshold;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;
use TypeLang\Bench\Mapper\Stub\AddressStub;
use TypeLang\Bench\Mapper\Stub\CircleStub;
use TypeLang\Bench\Mapper\Stub\ShapeStub;
use TypeLang\Bench\Mapper\Stub\SquareStub;
use TypeLang\Bench\Mapper\Stub\UserStub;
use TypeLang\Mapper\Mapper;

/**
 * Typical scenarios of the mapper on valid data: a class, collections, a
 * union of classes against a discriminator, a value without a type.
 */
#[Revs(200), Warmup(2), Iterations(10), RetryThreshold(3)]
#[BeforeMethods('prepare')]
#[Groups(['mapper'])]
final class MapperBench
{
    private Mapper $mapper;

    /**
     * @var list<UserStub>
     */
    private array $users = [];

    public function prepare(): void
    {
        $this->mapper = new Mapper();

        foreach (self::denormalizationDataProvider() as [$type, $data]) {
            $this->mapper->denormalize($data, $type);
        }

        /** @var list<UserStub> */
        $this->users = $this->mapper->denormalize(
            \array_fill(0, 100, self::createUser()),
            \sprintf('list<%s>', UserStub::class),
        );

        $this->mapper->normalize($this->users);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, mixed}>
     */
    public static function denormalizationDataProvider(): iterable
    {
        $circle = ['type' => 'circle', 'radius' => 1];
        $square = ['type' => 'square', 'side' => 2];
        $shapes = [];

        for ($i = 0; $i < 50; ++$i) {
            $shapes[] = $circle;
            $shapes[] = $square;
        }

        yield 'class' => [UserStub::class, self::createUser()];
        yield 'nested class' => [AddressStub::class, ['city' => 'Paris', 'street' => 'Rue de Rivoli']];
        yield 'list of 100 classes' => [\sprintf('list<%s>', UserStub::class), \array_fill(0, 100, self::createUser())];
        yield 'list of 100 ints' => ['list<int>', \range(1, 100)];
        yield 'map of 100 ints' => ['array<string, int>', \array_fill_keys(\array_map('strval', \range(1, 100)), 1)];
        yield 'shape' => ['array{id: int, name: string, tags?: list<string>}', ['id' => 1, 'name' => 'a']];
        yield '100 by discriminator' => [\sprintf('list<%s>', ShapeStub::class), $shapes];
        yield '100 by union of classes' => [\sprintf('list<%s|%s>', CircleStub::class, SquareStub::class), $shapes];
    }

    /**
     * @param array{non-empty-string, mixed} $params
     */
    #[ParamProviders('denormalizationDataProvider')]
    public function benchDenormalize(array $params): void
    {
        $this->mapper->denormalize($params[1], $params[0]);
    }

    public function benchNormalizeListOf100ClassesByType(): void
    {
        $this->mapper->normalize($this->users, 'list<' . UserStub::class . '>');
    }

    public function benchNormalizeListOf100ClassesWithoutType(): void
    {
        $this->mapper->normalize($this->users);
    }

    /**
     * @return array<non-empty-string, mixed>
     */
    private static function createUser(): array
    {
        return [
            'id' => 42,
            'name' => 'John',
            'email' => 'john@example.com',
            'isActive' => true,
            'rating' => 4.5,
            'address' => ['city' => 'Paris', 'street' => 'Rue de Rivoli'],
        ];
    }
}
