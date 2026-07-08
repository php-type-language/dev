<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver;

use TypeLang\Parser\TypeResolver\PhpUseStatementsReader;
use TypeLang\Parser\Tests\TypeResolver\Stub\ClassWithGroupUsesStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\ClosureUseStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\CommentsAroundUsesStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\FunctionAndConstUseStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\GroupUseStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\LeadingBackslashUseStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\MultipleNamespacesClassStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\NoImportsStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\SimpleClassStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\TraitUsageStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\TwoClassesInFileStub;

final class PhpUseStatementsReaderTest extends TypeResolverTestCase
{
    private PhpUseStatementsReader $reader {
        get => $this->reader ??= new PhpUseStatementsReader();
    }

    /**
     * @param class-string $class
     *
     * @return array<int|non-empty-string, non-empty-string>
     * @throws \ReflectionException
     */
    private function read(string $class): array
    {
        return $this->reader->getUseStatements(new \ReflectionClass($class));
    }

    /**
     * A plain `use X;` is returned under an integer key, while an aliased
     * `use X as Y;` is returned under its alias `Y`.
     */
    public function testReadsPlainAndAliasedImports(): void
    {
        self::assertSame([
            // use Some\Any;
            'Some\Any',
            // use Some\Any\Test as Example;
            'Example' => 'Some\Any\Test',
        ], $this->read(SimpleClassStub::class));
    }

    /**
     * Every import of the class is returned, preserving the order in which the
     * statements appear in the source.
     */
    public function testReadsAllImportsInSourceOrder(): void
    {
        self::assertSame([
            // use Example\Some\Any\Test1 as Example1;
            'Example1' => 'Example\Some\Any\Test1',
            // use Example\Some\Any1;
            'Example\Some\Any1',
            // use Some\Any\Test2 as Example2;
            'Example2' => 'Some\Any\Test2',
            // use Some\Any2;
            'Some\Any2',
        ], $this->read(ClassWithGroupUsesStub::class));
    }

    /**
     * When a file declares several namespace blocks, only the imports of the
     * block the class actually lives in are returned.
     */
    public function testReadsImportsOfTheOwningNamespaceBlock(): void
    {
        self::assertSame([
            // use Some\Any\Test2 as Example2;
            'Example2' => 'Some\Any\Test2',
            // use Some\Any2;
            'Some\Any2',
        ], $this->read(MultipleNamespacesClassStub::class));
    }

    /**
     * A class living in the global namespace block sees only that block's
     * imports, not those of the sibling namespaced block in the same file.
     *
     * @throws \ReflectionException
     */
    public function testReadsImportsOfTheGlobalNamespaceBlock(): void
    {
        // The global \Example class is declared in the same file as the stub
        // below; reflecting the stub forces that file (and \Example) to load.
        new \ReflectionClass(MultipleNamespacesClassStub::class);

        self::assertSame([
            // use Some\Any\Test1 as Example1;
            'Example1' => 'Some\Any\Test1',
            // use Some\Any1;
            'Some\Any1',
        ], $this->read(\Example::class));
    }

    /**
     * An internal (non-user-defined) class has no source file to read, so no
     * imports can be extracted.
     */
    public function testReturnsEmptyForInternalClass(): void
    {
        self::assertSame([], $this->read(\stdClass::class));
    }

    /**
     * A class with no imports at all yields an empty list.
     */
    public function testReturnsEmptyForClassWithoutImports(): void
    {
        self::assertSame([], $this->read(NoImportsStub::class));
    }

    /**
     * Comments (line, block and hash) interleaved with the `use` statements
     * are irrelevant and must not affect the extracted imports.
     */
    public function testIgnoresCommentsAroundImports(): void
    {
        self::assertSame([
            'Some\First',
            'Aliased' => 'Some\Second',
            'Some\Third',
        ], $this->read(CommentsAroundUsesStub::class));
    }

    /**
     * Namespace-level imports belong to every class in the block, so a class
     * declared after another one still reports them.
     */
    public function testReadsImportsSharedBySeveralClassesInOneFile(): void
    {
        self::assertSame([
            'Some\Shared',
            'Alias' => 'Some\Other',
        ], $this->read(TwoClassesInFileStub::class));
    }

    /**
     * A `use TraitName;` inside the class body imports a trait into the class,
     * not a type name into the file, and must not be reported.
     */
    public function testIgnoresTraitUsageInsideClassBody(): void
    {
        self::assertSame([
            'Some\ImportedClass',
        ], $this->read(TraitUsageStub::class));
    }

    /**
     * The `use (...)` clause of a closure captures variables and has nothing to
     * do with imports, so only the real import is reported.
     */
    public function testIgnoresClosureUseClause(): void
    {
        self::assertSame([
            'Some\RealImport',
        ], $this->read(ClosureUseStub::class));
    }

    /**
     * A class living in the global scope of a file that declares no namespace
     * at all still has its imports read.
     *
     * @throws \ReflectionException
     */
    public function testReadsImportsForClassWithoutNamespaceDeclaration(): void
    {
        require_once __DIR__ . '/Stub/GlobalScopeStub.php';

        // The class has no namespace, so it cannot be referenced as a compile
        // time `::class` constant; resolve it dynamically instead.
        $class = 'GlobalScopeStub';

        if (!\class_exists('GlobalScopeStub')) {
            self::fail('The global-scope stub class was not loaded');
        }

        self::assertSame([
            'Some\Any',
            'Alias' => 'Some\Any\Thing',
        ], $this->read($class));
    }

    /**
     * A group `use A\{B, C as D};` is a shorthand for listing each import
     * individually and is expanded into the same entries.
     */
    public function testExpandsGroupUseStatements(): void
    {
        self::assertSame([
            'Some\Group\First',
            'Alias' => 'Some\Group\Second',
            'Some\Group\Third',
        ], $this->read(GroupUseStub::class));
    }

    /**
     * `use function` and `use const` import symbols from other symbol tables,
     * not type names, so a type-import reader must leave them out.
     */
    public function testIgnoresFunctionAndConstImports(): void
    {
        self::assertSame([
            'Some\ClassName',
            'Alias' => 'Some\Aliased',
        ], $this->read(FunctionAndConstUseStub::class));
    }

    /**
     * A leading `\` in a `use` is redundant (imports are always absolute), so
     * `use \Some\Any;` must be read exactly like `use Some\Any;`.
     */
    public function testReadsImportsWrittenWithLeadingBackslash(): void
    {
        self::assertSame([
            'Some\Any',
            'Alias' => 'Some\Any\Thing',
        ], $this->read(LeadingBackslashUseStub::class));
    }
}
