<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Tests\Reader;

use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Mapping\Reader\PhpDocMetadataReader;
use TypeLang\Mapper\Mapping\Tests\Stub\BrokenDocBlockStub;
use TypeLang\Mapper\Mapping\Tests\Stub\BrokenPromotedStub;
use TypeLang\Mapper\Mapping\Tests\Stub\DocBlockStub;
use TypeLang\Mapper\Mapping\Tests\Stub\PromotedStub;
use TypeLang\Mapper\Mapping\Tests\Stub\TraitUserStub;
use TypeLang\Mapper\Mapping\Tests\Stub\UserStub;
use TypeLang\Mapper\Mapping\Tests\TestCase;

final class PhpDocMetadataReaderTest extends TestCase
{
    public function testFirstReaderReturnsNoProperties(): void
    {
        $metadata = (new PhpDocMetadataReader())->read(new \ReflectionClass(DocBlockStub::class));

        self::assertSame(DocBlockStub::class, $metadata->name);
        self::assertSame([], $metadata->properties);
    }

    public function testVarTagIsResolvedByImportsOfClassFile(): void
    {
        $metadata = self::readPhpDoc(DocBlockStub::class);

        $accounts = self::getProperty($metadata, 'accounts');
        self::assertType('list<' . UserStub::class . '>', $accounts->read);
        self::assertType('list<' . UserStub::class . '>', $accounts->write);
        self::assertSame('DocBlockStub.php', \basename((string) $accounts->read->source->file));
    }

    public function testNamesWithoutImportAreKeptAsWritten(): void
    {
        $metadata = self::readPhpDoc(DocBlockStub::class);

        self::assertType('T', self::getProperty($metadata, 'template')->read);
        self::assertType('self<T>|null', self::getProperty($metadata, 'self')->read);
    }

    public function testTypeKeepsNamespaceOfDeclaration(): void
    {
        $metadata = self::readPhpDoc(PromotedStub::class);

        self::assertSame(
            'TypeLang\Mapper\Mapping\Tests\Stub',
            self::getProperty($metadata, 'users')->write?->namespace,
        );
    }

    public function testEarlierTagNameWinsAndTagOfOtherVariableIsSkipped(): void
    {
        $metadata = self::readPhpDoc(DocBlockStub::class);

        self::assertType('non-empty-list<int>', self::getProperty($metadata, 'prefixed')->read);
        self::assertType('mixed', self::getProperty($metadata, 'foreign')->read);
    }

    public function testPromotedPropertyIsReadFromVarThenFromConstructorParam(): void
    {
        $metadata = self::readPhpDoc(PromotedStub::class);

        self::assertType('list<UserStub>', self::getProperty($metadata, 'users')->write);
        self::assertType('positive-int', self::getProperty($metadata, 'count')->read);
        self::assertType('TItem', self::getProperty($metadata, 'item')->read);
        self::assertType('string', self::getProperty($metadata, 'required')->read);
    }

    public function testUnparsableConstructorParamIsErrorOfPromotedPropertiesWithoutOwnTag(): void
    {
        $metadata = self::readPhpDoc(BrokenPromotedStub::class);

        self::assertNotNull(self::getProperty($metadata, 'users')->read?->error);
        self::assertNotNull(self::getProperty($metadata, 'nullable')->read?->error);
        self::assertType('positive-int', self::getProperty($metadata, 'count')->read);
        self::assertType('non-empty-string', self::getProperty($metadata, 'name')->read);
    }

    public function testTraitPropertyIsResolvedByImportsOfTraitFile(): void
    {
        $type = self::getProperty(self::readPhpDoc(TraitUserStub::class), 'owners')->read;

        self::assertType('list<' . UserStub::class . '>', $type);
        self::assertSame('OwnerTrait.php', \basename((string) $type->source->file));
    }

    public function testHigherPhpDocReaderOverridesLowerOne(): void
    {
        $class = new \ReflectionClass(BrokenDocBlockStub::class);
        $lower = self::readOverReflection(new PhpDocMetadataReader(varTags: ['broken-var']), $class->name);

        $metadata = (new PhpDocMetadataReader(varTags: ['type-lang-var']))->read($class, $lower);

        self::assertNotNull(self::getProperty($lower, 'value')->read?->error);
        self::assertType('positive-int', self::getProperty($metadata, 'value')->read);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return ClassMetadata<T>
     * @throws \Throwable
     */
    private static function readPhpDoc(string $class): ClassMetadata
    {
        return self::readOverReflection(new PhpDocMetadataReader(), $class);
    }
}
