<?php

declare(strict_types=1);

namespace TypeLang\Bench\Mapper\Coercer;

use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Groups;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\ParamProviders;
use PhpBench\Attributes\RetryThreshold;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;
use TypeLang\Mapper\Coercer\BoolCoercer;
use TypeLang\Mapper\Coercer\BoolTypeCoercer;
use TypeLang\Mapper\Coercer\CoercerInterface;
use TypeLang\Mapper\Coercer\FloatCoercer;
use TypeLang\Mapper\Coercer\FloatTypeCoercer;
use TypeLang\Mapper\Coercer\IntCoercer;
use TypeLang\Mapper\Coercer\IntTypeCoercer;
use TypeLang\Mapper\Coercer\StringCoercer;
use TypeLang\Mapper\Coercer\StringTypeCoercer;
use TypeLang\Mapper\Coercer\TypeCoercerInterface;
use TypeLang\Mapper\Context\RootContext;
use TypeLang\Mapper\Context\RuntimeContext;

/**
 * Compares coercers with the coercers of mapper 1.0 from `var/mapper-1.0`.
 */
#[Revs(10000), Warmup(2), Iterations(10), RetryThreshold(3)]
#[BeforeMethods('prepare')]
final class CoercerBench
{
    private const LEGACY_DIRECTORY = __DIR__ . '/../../../var/mapper-1.0/src';

    private const LEGACY_NAMESPACE = 'TypeLang\\Mapper\\';

    /**
     * @var array<non-empty-string, CoercerInterface<int|float|string|bool|array<array-key, mixed>>>
     */
    private array $coercers;

    /**
     * @var array<non-empty-string, TypeCoercerInterface>
     */
    private array $legacyCoercers;

    private RuntimeContext $context;

    public function prepare(): void
    {
        if (!\class_exists(IntTypeCoercer::class)) {
            self::registerLegacyAutoloader();
        }

        $this->coercers = [
            'int' => new IntCoercer(),
            'float' => new FloatCoercer(),
            'string' => new StringCoercer(),
            'bool' => new BoolCoercer(),
        ];

        $this->legacyCoercers = [
            'int' => new IntTypeCoercer(),
            'float' => new FloatTypeCoercer(),
            'string' => new StringTypeCoercer(),
            'bool' => new BoolTypeCoercer(),
        ];

        $this->context = (new \ReflectionClass(RootContext::class))
            ->newInstanceWithoutConstructor();
    }

    private static function registerLegacyAutoloader(): void
    {
        \spl_autoload_register(static function (string $class): void {
            if (!\str_starts_with($class, self::LEGACY_NAMESPACE)) {
                return;
            }

            $relative = \substr($class, \strlen(self::LEGACY_NAMESPACE));
            $pathname = self::LEGACY_DIRECTORY . '/' . \str_replace('\\', '/', $relative) . '.php';

            if (\is_file($pathname)) {
                require $pathname;
            }
        });
    }

    /**
     * @return iterable<non-empty-string, array{coercer: non-empty-string, value: mixed}>
     */
    public static function valueDataProvider(): iterable
    {
        yield 'int from string' => ['coercer' => 'int', 'value' => '42'];
        yield 'int from float' => ['coercer' => 'int', 'value' => 42.0];
        yield 'int from word' => ['coercer' => 'int', 'value' => 'abc'];
        yield 'float from string' => ['coercer' => 'float', 'value' => '1.5'];
        yield 'float from int' => ['coercer' => 'float', 'value' => 42];
        yield 'string from int' => ['coercer' => 'string', 'value' => 42];
        yield 'string from float' => ['coercer' => 'string', 'value' => 1.5];
        yield 'bool from word' => ['coercer' => 'bool', 'value' => 'true'];
        yield 'bool from int' => ['coercer' => 'bool', 'value' => 1];
    }

    /**
     * @param array{coercer: non-empty-string, value: mixed} $params
     */
    #[Groups(['baseline']), ParamProviders('valueDataProvider')]
    public function benchLegacyCoercer(array $params): void
    {
        $this->legacyCoercers[$params['coercer']]->tryCoerce($params['value'], $this->context);
    }

    /**
     * @param array{coercer: non-empty-string, value: mixed} $params
     */
    #[ParamProviders('valueDataProvider')]
    public function benchCoercer(array $params): void
    {
        $this->coercers[$params['coercer']]->coerce($params['value']);
    }
}
