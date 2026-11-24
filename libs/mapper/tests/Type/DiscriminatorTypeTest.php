<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Tests\Type;

use PHPUnit\Framework\Attributes\DataProvider;
use TypeLang\Mapper\Configuration;
use TypeLang\Mapper\Context\ClassAccessors;
use TypeLang\Mapper\Context\ClassTypes;
use TypeLang\Mapper\Context\Coercers;
use TypeLang\Mapper\Context\Context;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Exception\TypeDefinitionException;
use TypeLang\Mapper\Instantiator\InstantiatorFactory;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MetadataBuilder;
use TypeLang\Mapper\PropertyAccessor\PropertyAccessorFactory;
use TypeLang\Mapper\Tests\Stub\AddressStub;
use TypeLang\Mapper\Tests\Stub\Animal\AnimalStub;
use TypeLang\Mapper\Tests\Stub\Animal\PuppyStub;
use TypeLang\Mapper\Tests\Stub\Clash\ClashBaseStub;
use TypeLang\Mapper\Tests\Stub\Clash\ClashBothStub;
use TypeLang\Mapper\Tests\Stub\Clash\ClashLeftStub;
use TypeLang\Mapper\Tests\Stub\Clash\ClashPolygonStub;
use TypeLang\Mapper\Tests\Stub\Clash\ClashRightStub;
use TypeLang\Mapper\Tests\Stub\Clash\ClashSquareStub;
use TypeLang\Mapper\Tests\Stub\Clash\SelfBaseStub;
use TypeLang\Mapper\Tests\Stub\Clash\SelfPolygonStub;
use TypeLang\Mapper\Tests\Stub\Clash\SelfSameBaseStub;
use TypeLang\Mapper\Tests\Stub\Clash\SelfSamePolygonStub;
use TypeLang\Mapper\Tests\Stub\Event\EventStub;
use TypeLang\Mapper\Tests\Stub\Event\GenericEventStub;
use TypeLang\Mapper\Tests\Stub\Event\SpecialEventStub;
use TypeLang\Mapper\Tests\Stub\Figure\CircleFigureStub;
use TypeLang\Mapper\Tests\Stub\Figure\FigureStub;
use TypeLang\Mapper\Tests\Stub\Figure\HexagonFigureStub;
use TypeLang\Mapper\Tests\Stub\Figure\HoldsFiguresStub;
use TypeLang\Mapper\Tests\Stub\Figure\NumberFigureStub;
use TypeLang\Mapper\Tests\Stub\Figure\PolygonFigureStub;
use TypeLang\Mapper\Tests\Stub\Figure\QuadFigureStub;
use TypeLang\Mapper\Tests\Stub\Figure\RectFigureStub;
use TypeLang\Mapper\Tests\Stub\Figure\SquareFigureStub;
use TypeLang\Mapper\Tests\Stub\Figure\SquareProxyFigureStub;
use TypeLang\Mapper\Tests\Stub\Figure\TypedFigureStub;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Animal;
use TypeLang\Mapper\Tests\Stub\Hierarchy\AuditedCreated;
use TypeLang\Mapper\Tests\Stub\Hierarchy\BadMissingClassMap;
use TypeLang\Mapper\Tests\Stub\Hierarchy\BadOtherwiseUnrelated;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Blob;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Car;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Cat;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Circle;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Created;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Cube;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Deleted;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Doc;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Dog;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Event;
use TypeLang\Mapper\Tests\Stub\Hierarchy\HolderMixed;
use TypeLang\Mapper\Tests\Stub\Hierarchy\HolderNullablePolygon;
use TypeLang\Mapper\Tests\Stub\Hierarchy\HolderPolygon;
use TypeLang\Mapper\Tests\Stub\Hierarchy\HolderShape;
use TypeLang\Mapper\Tests\Stub\Hierarchy\HolderSquare;
use TypeLang\Mapper\Tests\Stub\Hierarchy\HolderUnion;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Lab;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Label;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Level;
use TypeLang\Mapper\Tests\Stub\Hierarchy\LevelOne;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Msg;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Named;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Pdf;
use TypeLang\Mapper\Tests\Stub\Hierarchy\PlainMsg;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Polygon;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Pug;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Round;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Shape;
use TypeLang\Mapper\Tests\Stub\Hierarchy\SpecialMsg;
use TypeLang\Mapper\Tests\Stub\Hierarchy\SportCar;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Square;
use TypeLang\Mapper\Tests\Stub\Hierarchy\SquareProxy;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Truck;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Txt;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Unrelated;
use TypeLang\Mapper\Tests\Stub\Hierarchy\Vehicle;
use TypeLang\Mapper\Tests\Stub\Otherwise\OtherwiseBaseStub;
use TypeLang\Mapper\Tests\Stub\Otherwise\OtherwiseChildStub;
use TypeLang\Mapper\Tests\Stub\Otherwise\OtherwiseStrayStub;
use TypeLang\Mapper\Tests\Stub\Shape\CircleStub;
use TypeLang\Mapper\Tests\Stub\Shape\FallbackShapeStub;
use TypeLang\Mapper\Tests\Stub\Shape\ForeignMapStub;
use TypeLang\Mapper\Tests\Stub\Shape\GroupStub;
use TypeLang\Mapper\Tests\Stub\Shape\LabeledStub;
use TypeLang\Mapper\Tests\Stub\Shape\MessageShapeStub;
use TypeLang\Mapper\Tests\Stub\Shape\MessageUnmappedStub;
use TypeLang\Mapper\Tests\Stub\Shape\ShapeStub;
use TypeLang\Mapper\Tests\Stub\Shape\SquareStub;
use TypeLang\Mapper\Tests\Stub\Shape\UnmappedFallbackStub;
use TypeLang\Mapper\Tests\Stub\Vehicle\CarStub;
use TypeLang\Mapper\Tests\Stub\Vehicle\ElectricCarStub;
use TypeLang\Mapper\Tests\Stub\Vehicle\HoverVehicleStub;
use TypeLang\Mapper\Tests\Stub\Vehicle\TruckStub;
use TypeLang\Mapper\Tests\Stub\Vehicle\VehicleStub;
use TypeLang\Mapper\Tests\TestCase;
use TypeLang\Mapper\TypeMatcher\NamedTypePattern;
use TypeLang\Mapper\Type\Builder\ClassTypeBuilder;
use TypeLang\Mapper\Type\Builder\NamedTypeBuilder;
use TypeLang\Mapper\Type\ClassType;
use TypeLang\Mapper\Type\IntType;
use TypeLang\Mapper\Type\Repository\TypeRepository;
use TypeLang\Parser\TypeParser;
use TypeLang\Printer\PrettyTypePrinter;

/**
 * Discriminator maps: tags of the data, classes of a hierarchy, errors of
 * wrong maps.
 */
final class DiscriminatorTypeTest extends TestCase
{
    private const SQUARE = ['type' => 'square', 'side' => 2, 'corners' => 4];
    private const CUBE = self::SQUARE;
    private const CUBE_DATA = ['type' => 'square', 'depth' => 3, 'side' => 2, 'corners' => 4];
    private const CIRCLE = ['type' => 'circle', 'radius' => 1, 'color' => 'red'];

    public function testTagSelectsClassOfData(): void
    {
        self::assertDenormalizes(ShapeStub::class, ['type' => 'circle', 'radius' => 1], self::createCircle(1));
    }

    public function testClassOfMapIsResolvedRelativeToNamespace(): void
    {
        self::assertDenormalizes(ShapeStub::class, (object) ['type' => 'square', 'side' => 2], self::createSquare(2));
    }

    public function testNestedPolymorphicDataIsDenormalized(): void
    {
        $group = new GroupStub();
        $group->children = [self::createCircle(1), self::createSquare(2)];

        self::assertDenormalizes(
            ShapeStub::class,
            [
                'type' => 'group',
                'children' => [['type' => 'circle', 'radius' => 1], ['type' => 'square', 'side' => 2]],
            ],
            $group,
        );
    }

    public function testMissingTagIsRejectedWithPathOfField(): void
    {
        self::assertDenormalizationFails(
            ShapeStub::class,
            ['radius' => 1],
            '$.type: Required field "type" is missing',
        );
    }

    public function testUnknownTagIsRejectedWithKnownTags(): void
    {
        self::assertDenormalizationFails(
            ShapeStub::class,
            ['type' => 'triangle'],
            '$.type: Value must be of type \'circle\'|\'square\'|\'group\', but "triangle" given',
        );
    }

    public function testErrorOfSelectedClassHasPathOfData(): void
    {
        self::assertDenormalizationFails(
            ShapeStub::class,
            ['type' => 'group', 'children' => [['type' => 'circle', 'radius' => 'x']]],
            '$.children[0].radius: Value must be of type int, but "x" given',
        );
    }

    public function testMessageOfBaseClassReplacesMessageOfMapper(): void
    {
        self::assertDenormalizationFails(MessageShapeStub::class, 'x', '$: Shape is expected, "x" given');
        self::assertNormalizationFails(MessageShapeStub::class, 42, '$: Shape is expected, 42 given');
    }

    public function testDataWithUnknownOrMissingTagIsMappedToDefault(): void
    {
        $fallback = new FallbackShapeStub();
        $fallback->label = 'plain';

        self::assertDenormalizes(FallbackShapeStub::class, ['kind' => 'unknown', 'label' => 'plain'], $fallback);
        self::assertDenormalizes(FallbackShapeStub::class, ['label' => 'plain'], $fallback);

        $labeled = new LabeledStub();
        $labeled->size = 3;

        self::assertDenormalizes(FallbackShapeStub::class, ['kind' => 'labeled', 'size' => 3], $labeled);
    }

    public function testTagIsAddedFirstOnNormalization(): void
    {
        self::assertNormalizes(ShapeStub::class, self::createCircle(1), ['type' => 'circle', 'radius' => 1]);
    }

    public function testTagIsAddedToObjectNormalizedAsStdClass(): void
    {
        $mapper = self::createMapper(config: new Configuration(normalizeAsArray: false));

        self::assertEquals(
            (object) ['type' => 'circle', 'radius' => 1],
            $mapper->normalize(self::createCircle(1), ShapeStub::class),
        );
    }

    public function testNestedPolymorphicObjectsAreNormalizedWithTags(): void
    {
        $group = new GroupStub();
        $group->children = [self::createCircle(1)];

        self::assertNormalizes(
            ShapeStub::class,
            $group,
            ['type' => 'group', 'children' => [['type' => 'circle', 'radius' => 1]]],
        );
    }

    public function testValueThatIsNotArrayOrObjectIsRejected(): void
    {
        self::assertDenormalizationFails(
            ShapeStub::class,
            'x',
            '$: Value must be of type object, but "x" given',
        );
    }

    public function testNormalizationRejectsObjectOfOtherHierarchy(): void
    {
        self::assertNormalizationFails(
            ShapeStub::class,
            new AddressStub(),
            '$: Value must be of type TypeLang\Mapper\Tests\Stub\Shape\ShapeStub, '
                . 'but object(TypeLang\Mapper\Tests\Stub\AddressStub) given',
        );
    }

    public function testObjectOfClassOutsideMapIsNormalizedByDefault(): void
    {
        $fallback = new FallbackShapeStub();

        self::assertNormalizes(FallbackShapeStub::class, $fallback, ['label' => 'none']);
    }

    public function testOwnClassIsNormalizedAsStdClassWithTag(): void
    {
        $mapper = self::createMapper(config: new Configuration(normalizeAsArray: false));

        self::assertEquals(
            (object) ['type' => 'circle', 'radius' => 1],
            $mapper->normalize(self::createCircle(1)),
        );
        self::assertEquals(
            (object) ['type' => 'circle', 'radius' => 1],
            $mapper->normalize(self::createCircle(1), CircleStub::class),
        );
    }

    public function testNestedMapsWriteTagsRootFirst(): void
    {
        $expected = ['kind' => 'car', 'fuel' => 'electric', 'range' => 300];

        self::assertNormalizes('mixed', new ElectricCarStub(), $expected);
        self::assertNormalizes(ElectricCarStub::class, new ElectricCarStub(), $expected);
        self::assertNormalizes(CarStub::class, new ElectricCarStub(), $expected);
        self::assertNormalizes(VehicleStub::class, new ElectricCarStub(), $expected);
        self::assertNormalizes('mixed', new TruckStub(), ['kind' => 'truck', 'load' => 10]);
    }

    public function testUnmappedChildOfBaseIsRejectedThroughBase(): void
    {
        self::assertNormalizationFails(
            VehicleStub::class,
            new HoverVehicleStub(),
            \sprintf(
                '$: Class "%s" has no tag in the discriminator map of "%s"',
                HoverVehicleStub::class,
                VehicleStub::class,
            ),
        );
    }

    public function testUnmappedChildOfBaseIsRejectedByItself(): void
    {
        $message = \sprintf(
            '$: Class "%s" has no tag in the discriminator map of "%s"',
            HoverVehicleStub::class,
            VehicleStub::class,
        );

        self::assertNormalizationFails('mixed', new HoverVehicleStub(), $message);
        self::assertNormalizationFails(HoverVehicleStub::class, new HoverVehicleStub(), $message);
    }

    public function testUnmappedChildIsRejectedWithMessageOfBase(): void
    {
        self::assertNormalizationFails(
            MessageShapeStub::class,
            new MessageUnmappedStub(),
            '$: Shape is expected, object(TypeLang\Mapper\Tests\Stub\Shape\MessageUnmappedStub) given',
        );
    }

    public function testUnmappedChildOfBaseWithDefaultIsNormalizedWithoutTag(): void
    {
        self::assertNormalizes('mixed', new UnmappedFallbackStub(), ['label' => 'none']);
    }

    public function testUnmappedChildOfClassOfDefaultIsNormalizedWithoutTag(): void
    {
        $mapper = self::createMapper();
        $object = new OtherwiseChildStub();

        self::assertSame(['fallback' => 2], $mapper->normalize($object, OtherwiseBaseStub::class));
        self::assertNormalizes('mixed', $object, ['fallback' => 2]);
    }

    public function testUnmappedChildOutsideClassOfDefaultIsRejected(): void
    {
        $object = new OtherwiseStrayStub();

        self::assertFalse(self::createMapper()->isNormalizable($object, OtherwiseBaseStub::class));
        self::assertFalse(self::createMapper()->isNormalizable($object));
        self::assertInstanceOf(
            InvalidValueException::class,
            self::captureThrowable(static fn(): mixed => self::createMapper()->normalize($object)),
        );
        self::assertInstanceOf(
            InvalidValueException::class,
            self::captureThrowable(
                static fn(): mixed => self::createMapper()->normalize($object, OtherwiseBaseStub::class),
            ),
        );
    }

    public function testClassMappedByItselfAndByParentWithDifferentTagsIsErrorOfType(): void
    {
        $message = \sprintf(
            'Class "%s" has two tags in field "type": "polygon" of class "%s" and "poly" of class "%s"',
            SelfPolygonStub::class,
            SelfBaseStub::class,
            SelfPolygonStub::class,
        );

        foreach ([SelfPolygonStub::class, SelfBaseStub::class] as $type) {
            $error = self::captureBuildError($type);

            self::assertInstanceOf(TypeDefinitionException::class, $error);
            self::assertSame($message, $error->getMessage());
        }
    }

    public function testClassMappedByItselfAndByParentWithSameTagIsNormalized(): void
    {
        $object = new SelfSamePolygonStub();

        self::assertNormalizes('mixed', $object, ['type' => 'poly', 'side' => 1]);
        self::assertNormalizes(SelfSameBaseStub::class, $object, ['type' => 'poly', 'side' => 1]);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, object, array<array-key, mixed>}>
     */
    public static function objectOfHierarchyDataProvider(): iterable
    {
        $rect = ['type' => 'rect', 'height' => 5, 'width' => 3, 'corners' => 4];
        $quad = ['type' => 'quad', 'width' => 3, 'corners' => 4];

        foreach (['mixed', FigureStub::class, PolygonFigureStub::class, QuadFigureStub::class] as $type) {
            yield 'quad through ' . $type => [$type, new QuadFigureStub(), $quad];
        }

        foreach (['mixed', FigureStub::class, PolygonFigureStub::class, QuadFigureStub::class] as $type) {
            yield 'rect through ' . $type => [$type, new RectFigureStub(), $rect];
        }

        yield 'rect through itself' => [RectFigureStub::class, new RectFigureStub(), $rect];
        yield 'integer tag' => [FigureStub::class, new NumberFigureStub(), ['type' => 1, 'value' => 7]];
        yield 'integer tag by itself' => [NumberFigureStub::class, new NumberFigureStub(), ['type' => 1, 'value' => 7]];
        yield 'own field of tag' => [FigureStub::class, new TypedFigureStub(), ['type' => 'own']];
        yield 'own field of tag by itself' => [TypedFigureStub::class, new TypedFigureStub(), ['type' => 'own']];
    }

    #[DataProvider('objectOfHierarchyDataProvider')]
    public function testObjectIsNormalizedSameThroughAnyClassOfHierarchy(
        string $type,
        object $object,
        array $data,
    ): void {
        self::assertNormalizes($type, $object, $data);
    }

    public function testPropertiesTypedByClassesOfHierarchyHaveTags(): void
    {
        $holder = new HoldsFiguresStub();
        $holder->figure = new RectFigureStub();
        $holder->polygon = new SquareFigureStub();
        $holder->square = new SquareProxyFigureStub();
        $holder->quad = new RectFigureStub();

        $rect = ['type' => 'rect', 'height' => 5, 'width' => 3, 'corners' => 4];
        $square = ['type' => 'square', 'side' => 2, 'corners' => 4];

        self::assertNormalizes(HoldsFiguresStub::class, $holder, [
            'figure' => $rect,
            'polygon' => $square,
            'square' => $square,
            'quad' => $rect,
        ]);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, array<array-key, mixed>, object}>
     */
    public static function dataOfHierarchyDataProvider(): iterable
    {
        $rect = ['type' => 'rect', 'height' => 5, 'width' => 3, 'corners' => 4];
        $quad = ['type' => 'quad', 'width' => 3, 'corners' => 4];

        foreach ([FigureStub::class, PolygonFigureStub::class, QuadFigureStub::class, RectFigureStub::class] as $type) {
            yield 'rect through ' . $type => [$type, $rect, new RectFigureStub()];
        }

        foreach ([FigureStub::class, PolygonFigureStub::class, QuadFigureStub::class] as $type) {
            yield 'quad through ' . $type => [$type, $quad, new QuadFigureStub()];
        }

        yield 'quad without tag' => [QuadFigureStub::class, ['width' => 3], new QuadFigureStub()];
        yield 'rect without tag' => [RectFigureStub::class, ['height' => 5], new RectFigureStub()];
        yield 'integer tag' => [FigureStub::class, ['type' => 1], new NumberFigureStub()];
        yield 'integer tag as string' => [FigureStub::class, ['type' => '1'], new NumberFigureStub()];
        yield 'integer tag as string by itself' => [NumberFigureStub::class, ['type' => '1'], new NumberFigureStub()];
        yield 'own field of tag by itself' => [TypedFigureStub::class, ['type' => 'any'], self::createTyped('any')];
    }

    /**
     * @param array<array-key, mixed>|object $data
     */
    #[DataProvider('dataOfHierarchyDataProvider')]
    public function testDataIsDenormalizedByTagThroughAnyClassOfHierarchy(
        string $type,
        array|object $data,
        object $expected,
    ): void {
        self::assertDenormalizes($type, $data, $expected);
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string, array<array-key, mixed>, non-empty-string}>
     */
    public static function wrongTagDataProvider(): iterable
    {
        yield 'circle to square' => [
            SquareFigureStub::class,
            ['type' => 'circle'],
            '$.type: Value must be of type \'square\', but "circle" given',
        ];
        yield 'rect to square' => [
            SquareFigureStub::class,
            ['type' => 'rect'],
            '$.type: Value must be of type \'square\', but "rect" given',
        ];
        yield 'circle to polygon' => [
            PolygonFigureStub::class,
            ['type' => 'circle'],
            '$.type: Value must be of type \'square\'|\'quad\'|\'rect\', but "circle" given',
        ];
        yield 'square to quad' => [
            QuadFigureStub::class,
            ['type' => 'square'],
            '$.type: Value must be of type \'quad\'|\'rect\', but "square" given',
        ];
        yield 'quad to rect' => [
            RectFigureStub::class,
            ['type' => 'quad'],
            '$.type: Value must be of type \'rect\', but "quad" given',
        ];
        yield 'unknown to figure' => [
            FigureStub::class,
            ['type' => 'oval'],
            '$.type: Value must be of type \'square\'|\'quad\'|\'rect\'|\'circle\'|1|\'typed\', but "oval" given',
        ];
        yield 'boolean tag to square' => [
            SquareFigureStub::class,
            ['type' => true],
            '$.type: Value must be of type \'square\', but true given',
        ];
        yield 'null tag to square' => [
            SquareFigureStub::class,
            ['type' => null],
            '$.type: Value must be of type \'square\', but null given',
        ];
        yield 'string of other number to number' => [
            NumberFigureStub::class,
            ['type' => '2'],
            '$.type: Value must be of type 1, but "2" given',
        ];
        yield 'missing tag to polygon' => [
            PolygonFigureStub::class,
            ['corners' => 4],
            '$.type: Required field "type" is missing',
        ];
        yield 'missing tag to figure' => [
            FigureStub::class,
            [],
            '$.type: Required field "type" is missing',
        ];
    }

    /**
     * @param array<array-key, mixed> $data
     */
    #[DataProvider('wrongTagDataProvider')]
    public function testDataWithTagOfOtherClassIsRejected(string $type, array $data, string $message): void
    {
        self::assertDenormalizationFails($type, $data, $message);
    }

    public function testWrongTagIsRejectedInProperty(): void
    {
        self::assertDenormalizationFails(
            HoldsFiguresStub::class,
            [
                'figure' => ['type' => 'circle'],
                'polygon' => ['type' => 'square'],
                'square' => ['type' => 'circle', 'radius' => 5],
            ],
            '$.square.type: Value must be of type \'square\', but "circle" given',
        );
    }

    /**
     * @return iterable<non-empty-string, array{non-empty-string}>
     */
    public static function hierarchyTypeDataProvider(): iterable
    {
        foreach ([FigureStub::class, PolygonFigureStub::class, QuadFigureStub::class, RectFigureStub::class] as $type) {
            yield $type => [$type];
        }
    }

    #[DataProvider('hierarchyTypeDataProvider')]
    public function testRoundTripThroughClassOfHierarchy(string $type): void
    {
        $mapper = self::createMapper(config: new Configuration(rejectExtraFields: true));
        $object = new RectFigureStub();

        self::assertEquals($object, $mapper->denormalize($mapper->normalize($object, $type), $type));
    }

    public function testObjectOfUnmappedClassWithMappedChildrenIsRejected(): void
    {
        $message = \sprintf(
            '$: Class "%s" has no tag in the discriminator map of "%s"',
            PolygonFigureStub::class,
            FigureStub::class,
        );

        foreach (['mixed', FigureStub::class, PolygonFigureStub::class] as $type) {
            self::assertNormalizationFails($type, new PolygonFigureStub(), $message);
        }
    }

    public function testObjectOfOtherBranchIsRejected(): void
    {
        self::assertNormalizationFails(
            PolygonFigureStub::class,
            new CircleFigureStub(),
            '$: Value must be of type TypeLang\Mapper\Tests\Stub\Figure\PolygonFigureStub, '
                . 'but object(TypeLang\Mapper\Tests\Stub\Figure\CircleFigureStub) given',
        );
    }

    public function testDenormalizationOfUnmappedClassWithoutMappedChildrenIsErrorOfType(): void
    {
        $mapper = self::createMapper();

        self::assertFalse($mapper->isNormalizable(new HexagonFigureStub(), HexagonFigureStub::class));

        $error = self::captureThrowable(static fn(): mixed => $mapper->denormalize([], HexagonFigureStub::class));

        self::assertInstanceOf(TypeDefinitionException::class, $error);
        self::assertStringContainsString(
            \sprintf(
                'Class "%s" is not in the discriminator map of "%s"',
                HexagonFigureStub::class,
                FigureStub::class,
            ),
            $error->getMessage(),
        );
    }

    public function testObjectOfUnmappedClassIsRejectedThroughBase(): void
    {
        self::assertNormalizationFails(
            FigureStub::class,
            new HexagonFigureStub(),
            \sprintf(
                '$: Class "%s" has no tag in the discriminator map of "%s"',
                HexagonFigureStub::class,
                FigureStub::class,
            ),
        );
    }

    public function testDataWithUnknownTagIsTakenByClassOfDefault(): void
    {
        $special = new SpecialEventStub();

        self::assertDenormalizes(EventStub::class, ['type' => 'other'], new GenericEventStub());
        self::assertDenormalizes(EventStub::class, [], new GenericEventStub());
        self::assertDenormalizes(EventStub::class, ['type' => 'special'], $special);
        self::assertDenormalizes(GenericEventStub::class, ['type' => 'other'], new GenericEventStub());
        self::assertDenormalizes(GenericEventStub::class, ['type' => 'generic'], new GenericEventStub());
    }

    public function testClassOfDefaultRejectsTagOfOtherClass(): void
    {
        self::assertDenormalizationFails(
            GenericEventStub::class,
            ['type' => 'special'],
            '$.type: Value must be of type \'generic\', but "special" given',
        );
    }

    public function testClassOfDefaultInMapIsNormalizedWithTag(): void
    {
        $expected = ['type' => 'generic', 'name' => 'generic'];

        self::assertNormalizes(EventStub::class, new GenericEventStub(), $expected);
        self::assertNormalizes(GenericEventStub::class, new GenericEventStub(), $expected);
    }

    public function testWrongTagIsCollectedWithOtherViolations(): void
    {
        $mapper = self::createMapper(config: new Configuration(maxViolations: 10, rejectExtraFields: true));
        $data = ['type' => 'circle', 'side' => 'x', 'extra' => 1];
        $error = self::captureThrowable(
            static fn(): mixed => $mapper->denormalize($data, SquareFigureStub::class),
        );

        self::assertInstanceOf(InvalidValueException::class, $error);
        self::assertCount(3, $error->violations);
    }

    private static function createTyped(string $type): TypedFigureStub
    {
        $object = new TypedFigureStub();
        $object->type = $type;

        return $object;
    }

    public function testRoundTripOfOwnClassWithRejectedExtraFields(): void
    {
        $mapper = self::createMapper(config: new Configuration(rejectExtraFields: true));
        $circle = self::createCircle(1);

        $data = $mapper->normalize($circle);

        self::assertSame(['type' => 'circle', 'radius' => 1], $data);
        self::assertEquals($circle, $mapper->denormalize($data, CircleStub::class));
        self::assertEquals($circle, $mapper->denormalize($data, ShapeStub::class));
    }

    public function testNestedMapsOnSameFieldAreErrorOfType(): void
    {
        $message = \sprintf(
            'Class "%s" has two tags in field "type": "polygon" of class "%s" and "square" of class "%s"',
            ClashSquareStub::class,
            ClashBaseStub::class,
            ClashPolygonStub::class,
        );

        foreach ([ClashSquareStub::class, ClashBaseStub::class] as $type) {
            $error = self::captureBuildError($type);

            self::assertInstanceOf(TypeDefinitionException::class, $error);
            self::assertSame($message, $error->getMessage());
        }
    }

    public function testInterfacesWithMapsOnSameFieldAreErrorOfType(): void
    {
        $error = self::captureBuildError(ClashBothStub::class);

        self::assertInstanceOf(TypeDefinitionException::class, $error);
        self::assertSame(
            \sprintf(
                'Class "%s" has two tags in field "type": "left" of class "%s" and "right" of class "%s"',
                ClashBothStub::class,
                ClashLeftStub::class,
                ClashRightStub::class,
            ),
            $error->getMessage(),
        );
    }

    public function testBuiltTypeWithInheritedTagsIsSerializable(): void
    {
        $parser = new TypeParser();
        $accessors = new ClassAccessors(
            properties: PropertyAccessorFactory::createDefault(),
            instantiators: InstantiatorFactory::createDefault(),
            hooks: [],
        );
        $repository = new TypeRepository(
            builders: [
                new NamedTypeBuilder(new NamedTypePattern(['int'], $parser), new IntType()),
                new ClassTypeBuilder(),
            ],
            metadata: MetadataBuilder::createDefault()->build(),
            decorators: [],
            accessors: $accessors,
            printer: new PrettyTypePrinter(),
        );
        $context = new Context(
            types: new ClassTypes($repository),
            config: new Configuration(),
            accessors: $accessors,
            coercers: Coercers::createDefault(),
        );

        $type = $repository->getType($parser->parse(ElectricCarStub::class));
        $copy = \unserialize(\serialize($type));

        self::assertInstanceOf(ClassType::class, $copy);
        self::assertSame(
            ['kind' => 'car', 'fuel' => 'electric', 'range' => 300],
            $copy->normalize(new ElectricCarStub(), $context),
        );
    }

    public function testClassOfMapOutsideHierarchyIsErrorOfType(): void
    {
        $error = self::captureBuildError(ForeignMapStub::class);

        self::assertInstanceOf(TypeDefinitionException::class, $error);
        self::assertSame(
            \sprintf(
                'Type of tag "address" of class "%s" is invalid: "%s" does not extend it',
                ForeignMapStub::class,
                AddressStub::class,
            ),
            $error->getMessage(),
        );
    }

    private static function createCircle(int $radius): CircleStub
    {
        $circle = new CircleStub();
        $circle->radius = $radius;

        return $circle;
    }

    private static function createSquare(int $side): SquareStub
    {
        $square = new SquareStub();
        $square->side = $side;

        return $square;
    }

    public function testObjectOfUnmappedChildTakesTagOfNearestMappedParent(): void
    {
        $puppy = new PuppyStub();
        $puppy->name = 'Rex';

        self::assertNormalizes(
            AnimalStub::class,
            $puppy,
            ['kind' => 'dog', 'barks' => true, 'name' => 'Rex'],
        );
    }

    /**
     * @return iterable<non-empty-string, array{string|null}>
     */
    public static function squareViewsDataProvider(): iterable
    {
        yield 'by the class of the object' => [null];
        yield 'mixed' => ['mixed'];
        yield 'root' => [Shape::class];
        yield 'intermediate class' => [Polygon::class];
        yield 'the class itself' => [Square::class];
        yield 'list element' => ['list<' . Shape::class . '>'];
    }

    #[DataProvider('squareViewsDataProvider')]
    public function testSquareIsNormalizedIdenticallyThroughAnyTypeOfItsHierarchy(?string $type): void
    {
        [$value, $expected] = self::wrap($type, new Square(), self::SQUARE);

        self::assertNormalizesTo($type, $value, $expected);
    }

    /**
     * @return iterable<non-empty-string, array{string|null}>
     */
    public static function cubeViewsDataProvider(): iterable
    {
        yield 'by the class of the object' => [null];
        yield 'root' => [Shape::class];
        yield 'intermediate class' => [Polygon::class];
        yield 'mapped parent' => [Square::class];
        yield 'the class itself' => [Cube::class];
        yield 'list of the intermediate' => ['list<' . Polygon::class . '>'];
    }

    #[DataProvider('cubeViewsDataProvider')]
    public function testClassOutsideTheMapIsNormalizedAsItsNearestMappedAncestor(?string $type): void
    {
        [$value, $expected] = self::wrap($type, new Cube(), self::CUBE);

        self::assertNormalizesTo($type, $value, $expected);
    }

    #[DataProvider('squareViewsDataProvider')]
    public function testProxyOfMappedClassIsNormalizedAsTheMappedClass(?string $type): void
    {
        [$value, $expected] = self::wrap($type, new SquareProxy(), self::SQUARE);

        self::assertNormalizesTo($type, $value, $expected);
    }

    public function testProxyIsNormalizedThroughItsOwnClass(): void
    {
        self::assertNormalizesTo(SquareProxy::class, new SquareProxy(), self::SQUARE);
    }

    /**
     * @return iterable<non-empty-string, array{string|null}>
     */
    public static function circleViewsDataProvider(): iterable
    {
        yield 'by the class of the object' => [null];
        yield 'root' => [Shape::class];
        yield 'abstract intermediate class' => [Round::class];
        yield 'the class itself' => [Circle::class];
    }

    #[DataProvider('circleViewsDataProvider')]
    public function testCircleIsNormalizedIdenticallyThroughAbstractIntermediateClass(?string $type): void
    {
        self::assertNormalizesTo($type, new Circle(), self::CIRCLE);
    }

    public function testTagIsTheFirstFieldOfNormalizedData(): void
    {
        foreach ([null, Shape::class, Polygon::class, Square::class] as $type) {
            $data = self::createMapperWith()->normalize(new Square(), $type);

            self::assertIsArray($data);
            self::assertSame('type', \array_key_first($data), (string) $type);
        }
    }

    public function testObjectOfSiblingClassIsNotNormalizedAsMoreSpecificType(): void
    {
        self::assertNormalizeRejected(Circle::class, new Square());
        self::assertNormalizeRejected(Round::class, new Square());
        self::assertNormalizeRejected(Polygon::class, new Circle());
    }

    public function testBaseObjectIsNotNormalizedAsItsChild(): void
    {
        self::assertNormalizeRejected(Square::class, new Polygon());
    }

    /**
     * @return iterable<non-empty-string, array{class-string, object}>
     */
    public static function holdersOfSquareDataProvider(): iterable
    {
        $make = static function (string $class, object $shape): object {
            $holder = new $class();
            $holder->shape = $shape;

            return $holder;
        };

        foreach ([HolderShape::class, HolderPolygon::class, HolderSquare::class, HolderUnion::class,
            HolderMixed::class, HolderNullablePolygon::class] as $class) {
            yield 'square in ' . $class => [$class, $make($class, new Square()), self::SQUARE];
            yield 'proxy in ' . $class => [$class, $make($class, new SquareProxy()), self::SQUARE];
            yield 'cube in ' . $class => [$class, $make($class, new Cube()), self::CUBE];
        }
    }

    /**
     * @param class-string $class
     * @param array<string, mixed> $expected
     */
    #[DataProvider('holdersOfSquareDataProvider')]
    public function testPropertyOfAnyTypeOfTheHierarchyNormalizesTheObjectTheSame(
        string $class,
        object $holder,
        array $expected,
    ): void {
        self::assertNormalizesTo($class, $holder, ['shape' => $expected]);
    }

    public function testCircleInUnionPropertyIsNormalizedWithTag(): void
    {
        $holder = new HolderUnion();
        $holder->shape = new Circle();

        self::assertNormalizesTo(HolderUnion::class, $holder, ['shape' => self::CIRCLE]);
    }

    public function testNullIsNormalizedInNullablePropertyOfIntermediateClass(): void
    {
        self::assertNormalizesTo(HolderNullablePolygon::class, new HolderNullablePolygon(), ['shape' => null]);
    }

    public function testNullIsNormalizedAsNullableIntermediateClass(): void
    {
        self::assertNormalizesTo('?' . Polygon::class, null, null);
    }

    public function testListOfIntermediateClassNormalizesEveryElementWithItsTag(): void
    {
        self::assertNormalizesTo(
            'list<' . Polygon::class . '>',
            [new Square(), new Cube(), new SquareProxy()],
            [self::SQUARE, self::CUBE, self::SQUARE],
        );
    }

    public function testListOfIntermediateClassRejectsElementOfSiblingBranch(): void
    {
        self::assertNormalizeRejected('list<' . Polygon::class . '>', [new Square(), new Circle()]);
    }

    public function testListOfRootRejectsElementThatIsNotInTheMap(): void
    {
        self::assertNormalizeRejected('list<' . Shape::class . '>', [new Square(), new Polygon()]);
    }

    public function testMixedValueNormalizesObjectsInsideArraysWithTheirTags(): void
    {
        self::assertNormalizesTo(
            'mixed',
            ['a' => new Square(), 'b' => [new Circle(), new Cube()]],
            ['a' => self::SQUARE, 'b' => [self::CIRCLE, self::CUBE]],
        );
        self::assertNormalizesTo(
            null,
            ['a' => new Square(), 'b' => [new Circle(), new Cube()]],
            ['a' => self::SQUARE, 'b' => [self::CIRCLE, self::CUBE]],
        );
    }

    public function testNormalizationDoesNotDependOnTheStrictness(): void
    {
        self::assertNormalizesTo(Polygon::class, new Square(), self::SQUARE, self::createMapperWith(strict: true));
    }

    /**
     * @return iterable<non-empty-string, array{string, array<string, mixed>|\stdClass, object}>
     */
    public static function denormalizationDataProvider(): iterable
    {
        yield 'root, square' => [Shape::class, self::SQUARE, new Square()];
        yield 'root, circle' => [Shape::class, self::CIRCLE, new Circle()];
        yield 'root, stdClass square' => [Shape::class, (object) self::SQUARE, new Square()];
        yield 'intermediate, square' => [Polygon::class, self::SQUARE, new Square()];
        yield 'intermediate, stdClass' => [Polygon::class, (object) self::SQUARE, new Square()];
        yield 'abstract intermediate, circle' => [Round::class, self::CIRCLE, new Circle()];
        yield 'the class itself' => [Square::class, self::SQUARE, new Square()];
        yield 'the class without tag' => [Square::class, ['side' => 2, 'corners' => 4], new Square()];
        yield 'circle without tag' => [Circle::class, ['radius' => 1, 'color' => 'red'], new Circle()];
        yield 'union, circle' => [Square::class . '|' . Circle::class, self::CIRCLE, new Circle()];
        yield 'union, square' => [Square::class . '|' . Circle::class, self::SQUARE, new Square()];
    }

    /**
     * @param array<string, mixed>|\stdClass $data
     */
    #[DataProvider('denormalizationDataProvider')]
    public function testDataIsDenormalizedToTheClassOfTheTag(
        string $type,
        array|\stdClass $data,
        object $expected,
    ): void {
        self::assertDenormalizes($type, $data, $expected, self::createMapperWith());
    }

    public function testDenormalizationOfCustomValuesRespectsFieldsOfTheClass(): void
    {
        $expected = new Square();
        $expected->side = 7;
        $expected->corners = 4;

        $data = ['type' => 'square', 'side' => 7, 'corners' => 4];

        self::assertDenormalizes(Shape::class, $data, $expected, self::createMapperWith());
    }

    public function testNullableIntermediateClassAcceptsNullAndTaggedData(): void
    {
        self::assertDenormalizes('?' . Polygon::class, null, null, self::createMapperWith());
        self::assertDenormalizes('?' . Polygon::class, self::SQUARE, new Square(), self::createMapperWith());
    }

    public function testListOfIntermediateClassDenormalizesEveryElement(): void
    {
        self::assertDenormalizes(
            'list<' . Polygon::class . '>',
            [self::SQUARE, self::CUBE_DATA],
            [new Square(), new Square()],
            self::createMapperWith(rejectExtraFields: false),
        );
    }

    public function testListOfIntermediateClassRejectsCircleWithPathOfTheElement(): void
    {
        self::assertDenormalizeRejected(
            'list<' . Polygon::class . '>',
            [self::SQUARE, self::CIRCLE],
            '$',
            contains: 'circle',
        );
    }

    public function testPropertyOfIntermediateClassDenormalizesTaggedData(): void
    {
        $expected = new HolderPolygon();
        $expected->shape = new Square();

        self::assertDenormalizes(HolderPolygon::class, ['shape' => self::SQUARE], $expected, self::createMapperWith());
    }

    public function testPropertyOfIntermediateClassRejectsOtherBranchWithPathOfTheProperty(): void
    {
        self::assertDenormalizeRejected(HolderPolygon::class, ['shape' => self::CIRCLE], '$.shape.type');
    }

    /**
     * @return iterable<non-empty-string, array{string, mixed}>
     */
    public static function rejectedTagDataProvider(): iterable
    {
        foreach ([Shape::class, Polygon::class, Round::class] as $type) {
            yield $type . ': unknown tag' => [$type, ['type' => 'oval']];
            yield $type . ': tag of another case' => [$type, ['type' => 'Square']];
            yield $type . ': empty tag' => [$type, ['type' => '']];
            yield $type . ': bool tag' => [$type, ['type' => true]];
            yield $type . ': false tag' => [$type, ['type' => false]];
            yield $type . ': null tag' => [$type, ['type' => null]];
            yield $type . ': array tag' => [$type, ['type' => ['square']]];
            yield $type . ': float tag' => [$type, ['type' => 1.5]];
            yield $type . ': int tag for string map' => [$type, ['type' => 0]];
            yield $type . ': object tag' => [$type, ['type' => new \stdClass()]];
            yield $type . ': missing tag' => [$type, ['side' => 2]];
            yield $type . ': empty data' => [$type, []];
        }
    }

    #[DataProvider('rejectedTagDataProvider')]
    public function testBadTagIsRejectedWithPathOfTheTag(string $type, mixed $data): void
    {
        self::assertDenormalizeRejected($type, $data, '$.type');
    }

    public function testSiblingTagIsRejectedByMoreSpecificTypes(): void
    {
        self::assertDenormalizeRejected(Polygon::class, self::CIRCLE, '$.type');
        self::assertDenormalizeRejected(Round::class, self::SQUARE, '$.type');
        self::assertDenormalizeRejected(Circle::class, self::SQUARE, '$.type');
        self::assertDenormalizeRejected(Square::class, self::CIRCLE, '$.type');
        self::assertDenormalizeRejected(Shape::class, ['type' => 'triangle'], '$.type');
    }

    /**
     * @return iterable<non-empty-string, array{mixed}>
     */
    public static function notAnObjectDataProvider(): iterable
    {
        yield 'string' => ['square'];
        yield 'int' => [1];
        yield 'null' => [null];
        yield 'true' => [true];
        yield 'float' => [1.5];
        yield 'empty string' => [''];
    }

    #[DataProvider('notAnObjectDataProvider')]
    public function testScalarDataIsRejectedWithRootPath(mixed $data): void
    {
        self::assertDenormalizeRejected(Shape::class, $data, '$');
        self::assertDenormalizeRejected(Polygon::class, $data, '$');
    }

    public function testListDataIsRejectedAsItHasNoTag(): void
    {
        self::assertDenormalizeRejected(Shape::class, ['square', 'circle'], '$');
    }

    public function testExtraFieldIsRejectedWhenConfigured(): void
    {
        self::assertDenormalizeRejected(Shape::class, [...self::SQUARE, 'zzz' => 1], '$', contains: 'zzz');
        self::assertDenormalizeRejected(Polygon::class, [...self::SQUARE, 'zzz' => 1], '$', contains: 'zzz');
        self::assertDenormalizeRejected(Square::class, [...self::SQUARE, 'zzz' => 1], '$', contains: 'zzz');
        self::assertDenormalizeRejected(Shape::class, (object) [...self::SQUARE, 'zzz' => 1], '$', contains: 'zzz');
    }

    public function testFieldOfSiblingClassIsExtraForTheClassOfTheTag(): void
    {
        self::assertDenormalizeRejected(Shape::class, [...self::SQUARE, 'radius' => 1], '$', contains: 'radius');
    }

    public function testTagIsNotAnExtraFieldAndExtraFieldsAreDroppedByDefault(): void
    {
        self::assertDenormalizes(Shape::class, self::SQUARE, new Square(), self::createMapperWith());
        self::assertDenormalizes(
            Shape::class,
            [...self::SQUARE, 'zzz' => 1, 'radius' => 9],
            new Square(),
            self::createMapperWith(rejectExtraFields: false),
        );
    }

    public function testStdClassDataWithoutRejectionDropsExtraFields(): void
    {
        self::assertDenormalizes(
            Polygon::class,
            (object) [...self::SQUARE, 'zzz' => 1],
            new Square(),
            self::createMapperWith(rejectExtraFields: false),
        );
    }

    public function testParentOfMappedClassIsNotNormalizedAsItsChild(): void
    {
        self::assertNormalizeRejected(SportCar::class, new Car());
    }

    public function testInterfaceCarriesTheMap(): void
    {
        $mapper = self::createMapperWith();

        self::assertNormalizesTo(Event::class, new Created(), ['type' => 'created', 'id' => 'c1'], $mapper);
        self::assertNormalizesTo(null, new Deleted(), ['type' => 'deleted', 'reason' => 'gone'], $mapper);
        self::assertNormalizesTo(
            Event::class,
            new AuditedCreated(),
            ['type' => 'created', 'id' => 'c1'],
            $mapper,
        );
        self::assertNormalizesTo(
            Created::class,
            new AuditedCreated(),
            ['type' => 'created', 'id' => 'c1'],
            $mapper,
        );
        self::assertDenormalizes(Event::class, ['type' => 'deleted', 'reason' => 'gone'], new Deleted(), $mapper);
        self::assertDenormalizes(Created::class, ['type' => 'created', 'id' => 'c1'], new Created(), $mapper);
        self::assertDenormalizes(
            'list<' . Event::class . '>',
            [['type' => 'created', 'id' => 'c1'], ['type' => 'deleted', 'reason' => 'gone']],
            [new Created(), new Deleted()],
            $mapper,
        );
        self::assertDenormalizeRejected(Event::class, ['type' => 'oval'], '$.type', $mapper);
        self::assertDenormalizeRejected(Deleted::class, ['type' => 'created'], '$.type', $mapper);
    }

    public function testNestedMapsWriteTagsOfBothFieldsAndRoundTrip(): void
    {
        $mapper = self::createMapperWith();
        $expected = ['kind' => 'dog', 'breed' => 'lab', 'weight' => 30];

        foreach ([null, Animal::class, Dog::class, Lab::class] as $type) {
            self::assertNormalizesTo($type, new Lab(), $expected, $mapper);
        }

        self::assertNormalizesTo(Animal::class, new Cat(), ['kind' => 'cat', 'name' => 'tom'], $mapper);

        foreach ([Animal::class, Dog::class, Lab::class] as $type) {
            self::assertDenormalizes($type, $expected, new Lab(), $mapper);
        }

        $pug = ['kind' => 'dog', 'breed' => 'pug', 'wrinkles' => 9];

        self::assertDenormalizes(Animal::class, $pug, new Pug(), $mapper);
        self::assertDenormalizes(Animal::class, ['kind' => 'cat', 'name' => 'tom'], new Cat(), $mapper);
    }

    public function testNestedMapRequiresItsOwnTag(): void
    {
        self::assertDenormalizeRejected(Animal::class, ['kind' => 'dog', 'weight' => 1], '$.breed');
        self::assertDenormalizeRejected(Animal::class, ['kind' => 'dog', 'breed' => 'cat'], '$.breed');
        self::assertDenormalizeRejected(Dog::class, ['kind' => 'cat', 'breed' => 'lab'], '$.kind');
        self::assertDenormalizeRejected(Animal::class, ['breed' => 'lab'], '$.kind');
    }

    public function testNestedMapAcceptsDataOfTheInnerMapWithoutOuterTag(): void
    {
        self::assertDenormalizes(Dog::class, ['breed' => 'lab', 'weight' => 30], new Lab(), self::createMapperWith());
    }

    public function testBothTagsAreNotExtraFieldsOfTheNestedMap(): void
    {
        $lab = ['kind' => 'dog', 'breed' => 'lab', 'weight' => 30];
        $extra = ['kind' => 'dog', 'breed' => 'lab', 'zzz' => 1];

        self::assertDenormalizes(Animal::class, $lab, new Lab(), self::createMapperWith());
        self::assertDenormalizeRejected(Animal::class, $extra, '$', contains: 'zzz');
    }

    public function testOtherwiseThatIsInTheMapTakesUnknownAndMissingTags(): void
    {
        $mapper = self::createMapperWith(rejectExtraFields: false);

        self::assertDenormalizes(Doc::class, ['type' => 'oval'], new Txt(), $mapper);
        self::assertDenormalizes(Doc::class, ['type' => 'oval', 'encoding' => 'ascii'], self::txt('ascii'), $mapper);
        self::assertDenormalizes(Doc::class, [], new Txt(), $mapper);
        self::assertDenormalizes(Doc::class, ['encoding' => 'ascii'], self::txt('ascii'), $mapper);
        self::assertDenormalizes(Doc::class, ['type' => 'pdf', 'pages' => 3], new Pdf(), $mapper);
        self::assertDenormalizes(Txt::class, ['type' => 'txt', 'encoding' => 'utf8'], new Txt(), $mapper);
    }

    public function testOtherwiseThatIsAParentOfMappedClassTakesUnknownTags(): void
    {
        $mapper = self::createMapperWith(rejectExtraFields: false);

        self::assertDenormalizes(Msg::class, ['type' => 'unknown'], new PlainMsg(), $mapper);
        self::assertDenormalizes(Msg::class, ['name' => 'plain'], new PlainMsg(), $mapper);
        $special = ['type' => 'special', 'name' => 'plain', 'extra' => 1];

        self::assertDenormalizes(Msg::class, $special, new SpecialMsg(), $mapper);
    }

    public function testOtherwiseThatIsNotInTheMapIsNormalizedWithoutTagAndReadBack(): void
    {
        $mapper = self::createMapperWith();

        self::assertNormalizesTo(Msg::class, new PlainMsg(), ['name' => 'plain'], $mapper);
        self::assertNormalizesTo(null, new PlainMsg(), ['name' => 'plain'], $mapper);
        self::assertNormalizesTo(
            Msg::class,
            new SpecialMsg(),
            ['type' => 'special', 'extra' => 1, 'name' => 'plain'],
            $mapper,
        );
        self::assertDenormalizes(Msg::class, ['name' => 'plain'], new PlainMsg(), $mapper);
    }

    public function testIntegerTagsAreWrittenAsIntegers(): void
    {
        $one = new LevelOne();

        foreach ([null, Level::class, LevelOne::class] as $type) {
            self::assertNormalizesTo($type, $one, ['kind' => 1, 'one' => 'a']);
        }
    }

    public function testIntegerTagsAreReadFromIntegersAndNumericStrings(): void
    {
        $mapper = self::createMapperWith();

        self::assertDenormalizes(Level::class, ['kind' => 1, 'one' => 'a'], new LevelOne(), $mapper);
        self::assertDenormalizes(Level::class, ['kind' => '1', 'one' => 'a'], new LevelOne(), $mapper);
    }

    /**
     * @return iterable<non-empty-string, array{mixed}>
     */
    public static function wrongIntegerTagDataProvider(): iterable
    {
        yield 'unknown int' => [3];
        yield 'zero' => [0];
        yield 'true is not 1' => [true];
        yield 'false is not 0' => [false];
        yield 'fractional float' => [1.5];
        yield 'null' => [null];
        yield 'array' => [[1]];
        yield 'text' => ['one'];
        yield 'padded number' => ['01'];
        yield 'number with space' => [' 1'];
        yield 'negative' => [-1];
    }

    #[DataProvider('wrongIntegerTagDataProvider')]
    public function testWrongIntegerTagIsRejected(mixed $tag): void
    {
        self::assertDenormalizeRejected(Level::class, ['kind' => $tag, 'one' => 'a'], '$.kind');
    }

    public function testMissingIntegerTagIsRejected(): void
    {
        self::assertDenormalizeRejected(Level::class, ['one' => 'a'], '$.kind');
    }

    public function testClassWithPropertyNamedAsTheTagFieldWritesTheTagOnce(): void
    {
        foreach ([null, Label::class, Named::class] as $type) {
            self::assertNormalizesTo($type, new Named(), ['type' => 'named', 'n' => 1]);
        }

        self::assertDenormalizes(Label::class, ['type' => 'named', 'n' => 1], new Named(), self::createMapperWith());
        self::assertDenormalizes(Named::class, ['type' => 'named', 'n' => 1], new Named(), self::createMapperWith());
    }

    public function testMapWithMissingClassIsADefinitionError(): void
    {
        self::assertInstanceOf(TypeDefinitionException::class, self::captureBuildError(BadMissingClassMap::class));
    }

    public function testOtherwiseOfAnotherHierarchyIsADefinitionError(): void
    {
        self::assertInstanceOf(TypeDefinitionException::class, self::captureBuildError(BadOtherwiseUnrelated::class));
    }

    /**
     * @return iterable<non-empty-string, array{string, object}>
     */
    public static function roundTripDataProvider(): iterable
    {
        foreach ([Shape::class, Polygon::class, Square::class] as $type) {
            yield 'square via ' . $type => [$type, new Square()];
        }

        foreach ([Shape::class, Round::class, Circle::class] as $type) {
            yield 'circle via ' . $type => [$type, new Circle()];
        }

        yield 'sportcar via car' => [Car::class, new SportCar()];
        yield 'lab via animal' => [Animal::class, new Lab()];
        yield 'lab via dog' => [Dog::class, new Lab()];
        yield 'txt via doc' => [Doc::class, new Txt()];
        yield 'plain via msg' => [Msg::class, new PlainMsg()];
        yield 'special via msg' => [Msg::class, new SpecialMsg()];
        yield 'level one' => [Level::class, new LevelOne()];
        yield 'named' => [Label::class, new Named()];
        yield 'created via event' => [Event::class, new Created()];
        yield 'deleted via event' => [Event::class, new Deleted()];
        yield 'truck via vehicle' => [Vehicle::class, new Truck()];
    }

    #[DataProvider('roundTripDataProvider')]
    public function testNormalizedDataIsReadBackByTheSameType(string $type, object $object): void
    {
        $mapper = self::createMapperWith();
        $data = $mapper->normalize($object, $type);

        self::assertTrue($mapper->isDenormalizable($data, $type));
        self::assertEquals($object, $mapper->denormalize($data, $type));
    }

    #[DataProvider('roundTripDataProvider')]
    public function testNormalizedDataIsReadBackFromJsonObject(string $type, object $object): void
    {
        $mapper = self::createMapperWith();
        $data = \json_decode(\json_encode($mapper->normalize($object, $type), \JSON_THROW_ON_ERROR), false);

        self::assertEquals($object, $mapper->denormalize($data, $type));
    }

    public function testProxyIsReadBackAsTheMappedClass(): void
    {
        $mapper = self::createMapperWith();
        $data = $mapper->normalize(new SquareProxy(), Shape::class);

        self::assertEquals(new Square(), $mapper->denormalize($data, Shape::class));
        self::assertEquals(new Square(), $mapper->denormalize($data, Polygon::class));
    }

    public function testClassOutsideTheMapIsReadBackThroughItsOwnClass(): void
    {
        $mapper = self::createMapperWith();
        $data = $mapper->normalize(new Cube(), Cube::class);

        self::assertTrue($mapper->isDenormalizable($data, Cube::class));
        self::assertEquals(new Cube(), $mapper->denormalize($data, Cube::class));
    }

    public function testClassOutsideTheMapIsReadBackThroughTheMappedParentAsTheParent(): void
    {
        $mapper = self::createMapperWith(rejectExtraFields: false);
        $data = $mapper->normalize(new Cube(), Square::class);

        self::assertEquals(new Square(), $mapper->denormalize($data, Square::class));
        self::assertEquals(new Square(), $mapper->denormalize($data, Shape::class));
    }

    public function testHolderIsReadBackThroughEveryTypeOfTheHierarchy(): void
    {
        $mapper = self::createMapperWith();

        foreach ([HolderShape::class, HolderPolygon::class, HolderSquare::class, HolderUnion::class] as $class) {
            $holder = new $class();
            $holder->shape = new Square();

            self::assertEquals($holder, $mapper->denormalize($mapper->normalize($holder, $class), $class), $class);
        }
    }

    public function testMultipleViolationsOfTheClassOfTheTagAreCollected(): void
    {
        $mapper = self::createMapperWith(maxViolations: 5);
        $data = ['type' => 'square', 'side' => 'x', 'corners' => 'y'];

        foreach ([Shape::class, Polygon::class, Square::class] as $type) {
            self::assertDenormalizeRejected($type, $data, '$', $mapper);

            $error = self::captureThrowable(static fn(): mixed => $mapper->denormalize($data, $type));

            self::assertStringContainsString('$.side', $error->getMessage(), $type);
            self::assertStringContainsString('and 1 more', $error->getMessage(), $type);
        }
    }

    public function testOnlyTheFirstViolationIsReportedByDefault(): void
    {
        $mapper = self::createMapperWith(maxViolations: 1);
        $error = self::captureThrowable(
            static fn(): mixed => $mapper->denormalize(
                ['type' => 'square', 'side' => 'x', 'corners' => 'y'],
                Shape::class,
            ),
        );

        self::assertNotSame(
            \str_contains($error->getMessage(), '$.side'),
            \str_contains($error->getMessage(), '$.corners'),
        );
    }

    public function testBadTagIsTheOnlyViolationAsTheClassIsUnknown(): void
    {
        $mapper = self::createMapperWith(maxViolations: 5);
        $error = self::captureThrowable(
            static fn(): mixed => $mapper->denormalize(['type' => 'oval', 'side' => 'x'], Shape::class),
        );

        self::assertStringContainsString('$.type', $error->getMessage());
        self::assertStringNotContainsString('$.side', $error->getMessage());
    }

    public function testViolationsInListElementsOfTheIntermediateClassAreCollected(): void
    {
        $mapper = self::createMapperWith(maxViolations: 5);
        $error = self::captureThrowable(static fn(): mixed => $mapper->denormalize(
            [['type' => 'circle'], ['type' => 'oval'], self::SQUARE],
            'list<' . Polygon::class . '>',
        ));

        self::assertInstanceOf(InvalidValueException::class, $error);
        self::assertStringContainsString('$[0]', $error->getMessage());
        self::assertStringContainsString('and 1 more', $error->getMessage());
        self::assertStringNotContainsString('$[2]', $error->getMessage());
    }

    /**
     * @return iterable<non-empty-string, array{string, mixed}>
     */
    public static function consistencyDataProvider(): iterable
    {
        $values = [
            'square data' => self::SQUARE,
            'circle data' => self::CIRCLE,
            'cube data' => self::CUBE_DATA,
            'unknown tag' => ['type' => 'oval'],
            'null tag' => ['type' => null],
            'int tag' => ['type' => 1],
            'no tag' => ['side' => 2],
            'empty' => [],
            'list' => [self::SQUARE],
            'null' => null,
            'string' => 'square',
            'stdClass square' => (object) self::SQUARE,
            'stdClass unknown' => (object) ['type' => 'oval'],
            'square object' => new Square(),
            'circle object' => new Circle(),
            'cube object' => new Cube(),
            'polygon object' => new Polygon(),
            'blob object' => new Blob(),
            'unrelated object' => new Unrelated(),
            'proxy object' => new SquareProxy(),
        ];

        foreach ([Shape::class, Polygon::class, Round::class, Square::class, Circle::class, Cube::class,
            Car::class, Doc::class, Msg::class, Level::class, 'mixed', '?' . Polygon::class,
            'list<' . Polygon::class . '>'] as $type) {
            foreach ($values as $name => $value) {
                yield $type . ' / ' . $name => [$type, $value];
            }
        }
    }

    #[DataProvider('consistencyDataProvider')]
    public function testPredicatesAgreeWithConversions(string $type, mixed $value): void
    {
        $mapper = self::createMapperWith();

        $conversion = static function (callable $fn): string {
            try {
                $fn();

                return 'accepted';
            } catch (InvalidValueException) {
                return 'rejected';
            } catch (TypeDefinitionException) {
                return 'definition error';
            }
        };
        $predicate = static function (callable $fn): string {
            try {
                return $fn() ? 'accepted' : 'rejected';
            } catch (TypeDefinitionException) {
                return 'definition error';
            }
        };

        self::assertSame(
            $conversion(static fn(): mixed => $mapper->normalize($value, $type)),
            $predicate(static fn(): bool => $mapper->isNormalizable($value, $type)),
            'normalize',
        );
        self::assertSame(
            $conversion(static fn(): mixed => $mapper->denormalize($value, $type)),
            $predicate(static fn(): bool => $mapper->isDenormalizable($value, $type)),
            'denormalize',
        );
    }

    private static function createMapperWith(
        bool $rejectExtraFields = true,
        int $maxViolations = 1,
        bool $normalizeAsArray = true,
        bool $strict = false,
    ): Mapper {
        return self::createMapper(config: new Configuration(
            strict: $strict,
            normalizeAsArray: $normalizeAsArray,
            maxViolations: $maxViolations,
            rejectExtraFields: $rejectExtraFields,
        ));
    }

    private static function assertNormalizesTo(
        ?string $type,
        mixed $value,
        mixed $expected,
        ?Mapper $mapper = null,
    ): void {
        $mapper ??= self::createMapperWith();

        self::assertTrue($mapper->isNormalizable($value, $type), 'isNormalizable() rejects');
        self::assertSame($expected, $mapper->normalize($value, $type));
    }

    private static function assertNormalizeRejected(?string $type, mixed $value, ?Mapper $mapper = null): void
    {
        $mapper ??= self::createMapperWith();

        self::assertFalse($mapper->isNormalizable($value, $type), 'isNormalizable() accepts');
        $error = self::captureThrowable(static fn(): mixed => $mapper->normalize($value, $type));
        self::assertInstanceOf(InvalidValueException::class, $error);
    }

    private static function assertDenormalizeRejected(
        string $type,
        mixed $value,
        string $path,
        ?Mapper $mapper = null,
        ?string $contains = null,
    ): void {
        $mapper ??= self::createMapperWith();

        self::assertFalse($mapper->isDenormalizable($value, $type), 'isDenormalizable() accepts');
        $error = self::captureThrowable(static fn(): mixed => $mapper->denormalize($value, $type));
        self::assertInstanceOf(InvalidValueException::class, $error);
        self::assertStringStartsWith($path, $error->getMessage());

        if ($contains !== null) {
            self::assertStringContainsString($contains, $error->getMessage());
        }
    }

    /**
     * @param non-empty-string|null $type
     * @return array{mixed, mixed}
     */
    private static function wrap(?string $type, mixed $value, mixed $expected): array
    {
        if ($type !== null && \str_starts_with($type, 'list<')) {
            return [[$value], [$expected]];
        }

        return [$value, $expected];
    }

    private static function txt(string $encoding): Txt
    {
        $txt = new Txt();
        $txt->encoding = $encoding;

        return $txt;
    }
}
