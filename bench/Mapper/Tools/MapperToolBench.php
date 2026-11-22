<?php

declare(strict_types=1);

namespace TypeLang\Bench\Mapper\Tools;

use PhpBench\Attributes\BeforeClassMethods;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\ParamProviders;
use PhpBench\Attributes\RetryThreshold;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;
use TypeLang\Bench\Mapper\Stub\AddressStub;
use TypeLang\Bench\Mapper\Stub\UserStub;

/**
 * Scenarios shared by the compared tools: the same classes and the same
 * valid data in both directions.
 *
 * The mode is the configuration of the tool: `dev` keeps nothing between
 * processes, `prod` reads the metadata from a persistent cache filled by
 * another process.
 *
 * The `benchBootAnd*` subjects create the tool for every call: the price
 * of the first value of a request. The other subjects call one tool
 * instance again and again: the price of a value in a worker or of every
 * value of a request after the first one.
 *
 * @phpstan-type ModeParamsType array{isCached: bool}
 */
#[BeforeClassMethods('prepareCache')]
#[Iterations(10), RetryThreshold(3)]
abstract class MapperToolBench
{
    /**
     * Format of the type of a list of the class in the syntax of the tool
     */
    protected const string LIST_TYPE = 'list<%s>';

    private const string CACHE_DIRECTORY = __DIR__ . '/../../var/mapper';

    private const int LIST_SIZE = 100;

    /**
     * Creates the tool instance used by {@see denormalize()} and
     * {@see normalize()}.
     *
     * @param non-empty-string|null $cache directory of the persistent cache,
     *        no persistent cache in case of {@see null}
     */
    abstract protected function boot(?string $cache): void;

    /**
     * @param non-empty-string $type
     */
    abstract protected function denormalize(mixed $data, string $type): mixed;

    /**
     * @param non-empty-string $type
     */
    abstract protected function normalize(mixed $value, string $type): mixed;

    /**
     * @return iterable<non-empty-string, ModeParamsType>
     */
    public static function modeDataProvider(): iterable
    {
        yield 'dev' => ['isCached' => false];
        yield 'prod' => ['isCached' => true];
    }

    /**
     * Replaces the persistent cache of the tool with a fresh one holding
     * everything the scenarios read from it.
     */
    public static function prepareCache(): void
    {
        $directory = self::getCacheDirectory();

        if (\is_dir($directory)) {
            self::clearDirectory($directory);
        }

        $bench = new static();
        $bench->boot($directory);

        foreach ([false, true] as $isList) {
            $bench->denormalize(self::createData($isList), self::getType($isList));
            $bench->normalize(self::createValue($isList), self::getType($isList));
        }
    }

    /**
     * @param ModeParamsType $params
     */
    public function prepareClass(array $params): void
    {
        $this->prepare($params['isCached'], false);
    }

    /**
     * @param ModeParamsType $params
     */
    public function prepareList(array $params): void
    {
        $this->prepare($params['isCached'], true);
    }

    /**
     * @param ModeParamsType $params
     */
    #[ParamProviders('modeDataProvider'), BeforeMethods('prepareClass'), Revs(2000), Warmup(500)]
    public function benchDenormalizeClass(array $params): void
    {
        $this->denormalize(self::createData(false), UserStub::class);
    }

    /**
     * @param ModeParamsType $params
     */
    #[ParamProviders('modeDataProvider'), BeforeMethods('prepareList'), Revs(50), Warmup(10)]
    public function benchDenormalizeListOf100Classes(array $params): void
    {
        $this->denormalize(self::createData(true), self::getType(true));
    }

    /**
     * @param ModeParamsType $params
     */
    #[ParamProviders('modeDataProvider'), BeforeMethods('prepareClass'), Revs(2000), Warmup(500)]
    public function benchNormalizeClass(array $params): void
    {
        $this->normalize(self::createValue(false), UserStub::class);
    }

    /**
     * @param ModeParamsType $params
     */
    #[ParamProviders('modeDataProvider'), BeforeMethods('prepareList'), Revs(50), Warmup(10)]
    public function benchNormalizeListOf100Classes(array $params): void
    {
        $this->normalize(self::createValue(true), self::getType(true));
    }

    /**
     * @param ModeParamsType $params
     */
    #[ParamProviders('modeDataProvider'), BeforeMethods('prepareClass'), Revs(100), Warmup(50)]
    public function benchBootAndDenormalizeClass(array $params): void
    {
        $this->boot(self::findCacheDirectory($params['isCached']));
        $this->denormalize(self::createData(false), UserStub::class);
    }

    /**
     * @param ModeParamsType $params
     */
    #[ParamProviders('modeDataProvider'), BeforeMethods('prepareClass'), Revs(100), Warmup(50)]
    public function benchBootAndNormalizeClass(array $params): void
    {
        $this->boot(self::findCacheDirectory($params['isCached']));
        $this->normalize(self::createValue(false), UserStub::class);
    }

    /**
     * @throws \LogicException in case of the tool returns another result
     */
    private function prepare(bool $isCached, bool $isList): void
    {
        $this->boot(self::findCacheDirectory($isCached));

        $type = self::getType($isList);
        $data = self::createData($isList);
        $value = self::createValue($isList);

        self::assertEquals($value, $this->denormalize($data, $type));
        self::assertEquals($data, $this->normalize($value, $type));
    }

    /**
     * @return non-empty-string|null
     */
    private static function findCacheDirectory(bool $isCached): ?string
    {
        if ($isCached) {
            return self::getCacheDirectory();
        }

        return null;
    }

    /**
     * @return non-empty-string
     */
    private static function getCacheDirectory(): string
    {
        $directory = self::CACHE_DIRECTORY . '/' . new \ReflectionClass(static::class)->getShortName();

        if (!\is_dir($directory)) {
            \mkdir($directory, recursive: true);
        }

        return $directory;
    }

    private static function clearDirectory(string $directory): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );

        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->isDir()) {
                \rmdir($file->getPathname());

                continue;
            }

            \unlink($file->getPathname());
        }
    }

    /**
     * @return non-empty-string
     */
    private static function getType(bool $isList): string
    {
        if ($isList) {
            return \sprintf(static::LIST_TYPE, UserStub::class);
        }

        return UserStub::class;
    }

    /**
     * @return array<array-key, mixed>
     */
    private static function createData(bool $isList): array
    {
        static $user = [
            'id' => 42,
            'name' => 'John',
            'email' => 'john@example.com',
            'isActive' => true,
            'rating' => 4.5,
            'address' => ['city' => 'Paris', 'street' => 'Rue de Rivoli'],
        ];

        static $users = \array_fill(0, self::LIST_SIZE, $user);

        if ($isList) {
            return $users;
        }

        return $user;
    }

    /**
     * @return UserStub|list<UserStub>
     */
    private static function createValue(bool $isList): UserStub|array
    {
        static $user = self::createUser();
        static $users = \array_map(self::createUser(...), \range(1, self::LIST_SIZE));

        if ($isList) {
            return $users;
        }

        return $user;
    }

    private static function createUser(): UserStub
    {
        $address = new AddressStub();
        $address->city = 'Paris';
        $address->street = 'Rue de Rivoli';

        $user = new UserStub();
        $user->id = 42;
        $user->name = 'John';
        $user->email = 'john@example.com';
        $user->isActive = true;
        $user->rating = 4.5;
        $user->address = $address;

        return $user;
    }

    /**
     * @throws \LogicException in case of the tool returns another result
     */
    private static function assertEquals(mixed $expected, mixed $actual): void
    {
        if ($expected == $actual) {
            return;
        }

        // The first item of a list is enough to see the difference
        if (\is_array($expected) && \is_array($actual) && \array_is_list($expected) && \array_is_list($actual)) {
            [$expected, $actual] = [$expected[0] ?? null, $actual[0] ?? null];
        }

        throw new \LogicException(\sprintf(
            "%s returns an unexpected result\nExpected: %s\nActual: %s",
            static::class,
            \var_export($expected, true),
            \var_export($actual, true),
        ));
    }
}
