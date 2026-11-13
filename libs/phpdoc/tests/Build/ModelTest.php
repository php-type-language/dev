<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\Build;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\PhpDoc\Build\Model\LinkedModel;
use TypeLang\PhpDoc\Build\Model\ModelLinker;
use TypeLang\PhpDoc\Build\Model\ModelLoader;
use TypeLang\PhpDoc\Build\Model\Tag;
use TypeLang\PhpDoc\Build\Snapshot\RegistryDumper;
use TypeLang\PhpDoc\DocBlock\TagDefinition\TagDefinitionInterface;
use TypeLang\PhpDoc\DocBlockParser;
use TypeLang\PhpDoc\Tests\TestCase;

/**
 * The model describes exactly the tags the library registers.
 */
final class ModelTest extends TestCase
{
    public const MODEL_DIRECTORY = __DIR__ . '/../../resources/model';

    private const SNAPSHOT_DIRECTORY = __DIR__ . '/../Platform/snapshot';

    private static ?LinkedModel $model = null;

    public static function getModel(): LinkedModel
    {
        return self::$model ??= (new ModelLinker())->link(
            (new ModelLoader())->load(self::MODEL_DIRECTORY),
        );
    }

    #[DataProvider('packageDataProvider')]
    public function testPackageMatchesRegistrySnapshot(string $package): void
    {
        $platform = new ModelPlatform(self::getModel()->packages[$package]);

        $actual = (new RegistryDumper())->dump(new DocBlockParser([$platform]));

        self::assertStringEqualsFile(self::SNAPSHOT_DIRECTORY . '/' . $package . '.txt', $actual);
    }

    #[DataProvider('tagDataProvider')]
    public function testTagMatchesItsDefinition(string $name): void
    {
        $tag = self::getModel()->tags[$name];
        $definition = new ($tag->getDefinitionClass())();

        self::assertInstanceOf(TagDefinitionInterface::class, $definition);
        self::assertSame($tag->name, $definition->name);
        self::assertSame($tag->placement, $definition->placement);
        self::assertEquals($tag->grammar, $definition->spec);
    }

    public function testBaseCombinatorsMatchParser(): void
    {
        $expected = [];

        foreach (self::getModel()->getBaseCombinators() as $combinator) {
            $expected[] = \sprintf('%s: %s', $combinator->name, $combinator->class);
        }

        $dump = (new RegistryDumper())->dump(new DocBlockParser());
        $actual = [];

        foreach (\explode("\n", $dump) as $line) {
            if (\str_contains($line, ': ') && !\str_starts_with($line, ' ')) {
                $actual[] = $line;
            }
        }

        // Description is wired by the parser as a closure.
        $expected = \array_values(\array_filter(
            $expected,
            static fn(string $line): bool => !\str_starts_with($line, 'Description: '),
        ));
        $actual = \array_values(\array_filter(
            $actual,
            static fn(string $line): bool => !\str_starts_with($line, 'Description: '),
        ));

        self::assertSame($expected, $actual);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function packageDataProvider(): iterable
    {
        foreach (\array_keys(self::getModel()->packages) as $package) {
            yield $package => [$package];
        }
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function tagDataProvider(): iterable
    {
        foreach (self::getModel()->tags as $name => $tag) {
            \assert($tag instanceof Tag);

            yield '@' . $name => [$name];
        }
    }
}
