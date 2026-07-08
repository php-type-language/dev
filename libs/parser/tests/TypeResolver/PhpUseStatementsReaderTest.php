<?php

declare(strict_types=1);

namespace TypeLang\Parser\Tests\TypeResolver;

use TypeLang\Parser\TypeResolver\PhpUseStatementsReader;
use TypeLang\Parser\Tests\TypeResolver\Stub\ClassWithGroupUsesStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\MultipleNamespacesClassStub;
use TypeLang\Parser\Tests\TypeResolver\Stub\SimpleClassStub;

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
}
