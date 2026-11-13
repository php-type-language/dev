<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Mapping\Tests\Reader;

use TypeLang\Mapper\Mapping\Exception\InvalidAttributeException;
use TypeLang\Mapper\Mapping\Exception\InvalidMetadataException;
use TypeLang\Mapper\Mapping\Exception\InvalidValueException;
use TypeLang\Mapper\Mapping\Exception\UnknownPropertyException;
use TypeLang\Mapper\Mapping\MapName;
use TypeLang\Mapper\Mapping\MapType;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Mapping\Reader\AttributeMetadataReader;
use TypeLang\Mapper\Mapping\Tests\Stub\AttributeStub;
use TypeLang\Mapper\Mapping\Tests\Stub\BrokenAttributeStub;
use TypeLang\Mapper\Mapping\Tests\Stub\MethodAttributeStub;
use TypeLang\Mapper\Mapping\Tests\Stub\StrictAttributeStub;
use TypeLang\Mapper\Mapping\Tests\Stub\TraitUserStub;
use TypeLang\Mapper\Mapping\Tests\Stub\UnparsableTypeStub;
use TypeLang\Mapper\Mapping\Tests\Stub\UnselectedAttributeStub;
use TypeLang\Mapper\Mapping\Tests\Stub\UserStub;
use TypeLang\Mapper\Mapping\Tests\TestCase;

final class AttributeMetadataReaderTest extends TestCase
{
    public function testMapNameAndMapTypeSetNameTypesAndStrictness(): void
    {
        $metadata = self::readAttributes(AttributeStub::class);

        self::assertSame(['id', 'accounts', 'value', 'count'], \array_keys($metadata->properties));
        self::assertSame('user_id', self::getProperty($metadata, 'id')->externalName);

        $accounts = self::getProperty($metadata, 'accounts');
        self::assertType('list<' . UserStub::class . '>', $accounts->read);
        self::assertType('list<' . UserStub::class . '>', $accounts->write);
        self::assertSame('AttributeStub.php', \basename((string) $accounts->read->source->file));
        self::assertSame('TypeLang\Mapper\Mapping\Tests\Stub', $accounts->read->namespace);
        self::assertNull($accounts->read->isStrict);

        $value = self::getProperty($metadata, 'value');
        self::assertType('int', $value->read);
        self::assertType('int', $value->write);
        self::assertTrue($value->read->isStrict);
        self::assertTrue($value->write->isStrict);
    }

    public function testNamedStrictArgumentAppliesToBothTypes(): void
    {
        $value = self::getProperty(self::readAttributes(StrictAttributeStub::class), 'value');

        self::assertType('positive-int', $value->read);
        self::assertType('positive-int', $value->write);
        self::assertTrue($value->read->isStrict);
        self::assertTrue($value->write->isStrict);
    }

    public function testAttributeKeepsValuesItDoesNotSet(): void
    {
        $id = self::getProperty(self::readAttributes(AttributeStub::class), 'id');

        self::assertType('int', $id->read);
        self::assertSame(0, $id->default?->value);
    }

    public function testAttributeOfTraitPropertyUsesImportsOfTraitFile(): void
    {
        $type = self::getProperty(self::readAttributes(TraitUserStub::class), 'main')->read;

        self::assertType(UserStub::class . '|self', $type);
        self::assertSame('OwnerTrait.php', \basename((string) $type->source->file));
        self::assertSame('TypeLang\Mapper\Mapping\Tests\Stub\Concern', $type->namespace);
    }

    public function testBrokenAttributesAreThrownTogether(): void
    {
        $read = static fn() => self::readAttributes(BrokenAttributeStub::class);
        $exceptions = self::getExceptions($read);
        $errors = self::getErrors($read);

        self::assertCount(2, $errors);
        self::assertInstanceOf(InvalidAttributeException::class, $exceptions[0]);
        self::assertInstanceOf(\Throwable::class, $exceptions[0]->getPrevious());
        self::assertStringStartsWith(
            \sprintf('%s::$id: attribute %s cannot be created (', BrokenAttributeStub::class, MapName::class),
            $errors[0],
        );
        self::assertInstanceOf(InvalidValueException::class, $exceptions[1]);
        self::assertSame(
            \sprintf(
                '%s::$name: value of "name" must be a non-empty string in BrokenAttributeStub.php',
                BrokenAttributeStub::class,
            ),
            $errors[1],
        );
    }

    public function testUnparsableTypeIsTypeWithErrorAndIsNotThrown(): void
    {
        $items = self::getProperty(self::readAttributes(UnparsableTypeStub::class), 'items');

        self::assertNotNull($items->read?->error);
        self::assertSame('UnparsableTypeStub.php', \basename((string) $items->read->source->file));
        self::assertSame($items->read, $items->write);
    }

    public function testMapTypeOfMethodIsThrownAndPropertyIsStillRead(): void
    {
        $read = static fn() => self::readAttributes(MethodAttributeStub::class);
        $exceptions = self::getExceptions($read);

        self::assertCount(1, $exceptions);
        self::assertInstanceOf(InvalidAttributeException::class, $exceptions[0]);
        self::assertSame([
            \sprintf(
                '%s::getId(): attribute %s is allowed only on a property or its hook in MethodAttributeStub.php',
                MethodAttributeStub::class,
                MapType::class,
            ),
        ], self::getErrors($read));

        try {
            $read();
        } catch (InvalidMetadataException $e) {
            self::assertType('positive-int', self::getProperty($e->metadata, 'id')->read);
        }
    }

    public function testAttributeOfPropertyThatIsNotMappedIsThrown(): void
    {
        $read = static fn() => self::readAttributes(UnselectedAttributeStub::class);
        $class = UnselectedAttributeStub::class;

        self::assertContainsOnlyInstancesOf(UnknownPropertyException::class, self::getExceptions($read));
        self::assertSame([
            "{$class}::\$secret: property is not mapped in UnselectedAttributeStub.php",
            "{$class}::\$hidden: property is not mapped in UnselectedAttributeStub.php",
            "{$class}::\$counter: property is not mapped in UnselectedAttributeStub.php",
        ], self::getErrors($read));
    }

    public function testFirstReaderMapsNoPropertiesSoEveryAttributeIsThrown(): void
    {
        $reader = new AttributeMetadataReader();
        $errors = self::getErrors(static fn() => $reader->read(new \ReflectionClass(AttributeStub::class)));
        $class = AttributeStub::class;

        self::assertSame([
            "{$class}::\$id: property is not mapped in AttributeStub.php",
            "{$class}::\$accounts: property is not mapped in AttributeStub.php",
            "{$class}::\$value: property is not mapped in AttributeStub.php",
            "{$class}::\$count: property is not mapped in AttributeStub.php",
        ], $errors);
    }

    public function testFirstReaderOfClassWithoutAttributesReturnsNoProperties(): void
    {
        $metadata = (new AttributeMetadataReader())->read(new \ReflectionClass(UserStub::class));

        self::assertSame(UserStub::class, $metadata->name);
        self::assertSame([], $metadata->properties);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return ClassMetadata<T>
     * @throws \Throwable
     */
    private static function readAttributes(string $class): ClassMetadata
    {
        return self::readOverReflection(new AttributeMetadataReader(), $class);
    }
}
