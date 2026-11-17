<?php

declare(strict_types=1);

namespace TypeLang\Mapper;

use JetBrains\PhpStorm\Language;
use TypeLang\Mapper\Context\ClassAccessors;
use TypeLang\Mapper\Context\Coercers;
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Exception\TypeDefinitionException;
use TypeLang\Mapper\Hook\ClassHookInterface;
use TypeLang\Mapper\Instantiator\InstantiatorFactory;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;
use TypeLang\Mapper\Mapping\Provider\MetadataProviderInterface;
use TypeLang\Mapper\PropertyAccessor\PropertyAccessorFactory;
use TypeLang\Mapper\Type\ArrayKeyType;
use TypeLang\Mapper\Type\BoolType;
use TypeLang\Mapper\Type\Builder\ArrayTypeBuilder;
use TypeLang\Mapper\Type\Builder\BackedEnumTypeBuilder;
use TypeLang\Mapper\Type\Builder\ClassTypeBuilder;
use TypeLang\Mapper\Type\Builder\ConstantTypeBuilder;
use TypeLang\Mapper\Type\Builder\DateTimeTypeBuilder;
use TypeLang\Mapper\Type\Builder\DiscriminatorTypeBuilder;
use TypeLang\Mapper\Type\Builder\IntRangeTypeBuilder;
use TypeLang\Mapper\Type\Builder\IterableTypeBuilder;
use TypeLang\Mapper\Type\Builder\ListTypeBuilder;
use TypeLang\Mapper\Type\Builder\LiteralTypeBuilder;
use TypeLang\Mapper\Type\Builder\NamedTypeBuilder;
use TypeLang\Mapper\Type\Builder\NullTypeBuilder;
use TypeLang\Mapper\Type\Builder\ObjectTypeBuilder;
use TypeLang\Mapper\Type\Builder\ShapeTypeBuilder;
use TypeLang\Mapper\Type\Builder\TypeBuilderInterface;
use TypeLang\Mapper\Type\Builder\TypesListTypeBuilder;
use TypeLang\Mapper\Type\Builder\UnionTypeBuilder;
use TypeLang\Mapper\Type\Builder\UnitEnumTypeBuilder;
use TypeLang\Mapper\Type\Check\IntRangeCheck;
use TypeLang\Mapper\Type\Check\NonEmptyCheck;
use TypeLang\Mapper\Type\Check\NumericCheck;
use TypeLang\Mapper\Type\Decorator\TypeDecoratorInterface;
use TypeLang\Mapper\Type\FloatType;
use TypeLang\Mapper\Type\IntType;
use TypeLang\Mapper\Type\MixedType;
use TypeLang\Mapper\Type\RefinedType;
use TypeLang\Mapper\Type\Repository\TypeRepository;
use TypeLang\Mapper\Type\StringType;
use TypeLang\Mapper\Type\TypeInterface;
use TypeLang\Mapper\TypeMatcher\Exception\InvalidPatternException;
use TypeLang\Mapper\TypeMatcher\NamedTypePattern;
use TypeLang\Parser\Exception\ParserExceptionInterface;
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeParserInterface;
use TypeLang\Type\TypeNode;

/**
 * Facade: parses the type string once, builds the type once and converts
 * values by it in both directions.
 */
final class Mapper implements DenormalizerInterface, NormalizerInterface
{
    private readonly TypeRepository $repository;

    /**
     * @var array<non-empty-string, TypeInterface<mixed>>
     */
    private array $types = [];

    /**
     * @param list<TypeBuilderInterface<mixed>> $builders builders of the
     *        user, tried before the default ones
     * @param list<TypeDecoratorInterface> $decorators wrap the built types
     *        in the order of the list, such as all enums
     * @param list<ClassHookInterface> $hooks run around the conversion of
     *        the objects of the classes they support, in the order of the
     *        list
     * @param Coercers $coercers how values written in another form become
     *        values of the types
     * @throws InvalidPatternException in case of a pattern of a default type
     *         is written wrong
     */
    public function __construct(
        Configuration $config = new Configuration(),
        ?MetadataProviderInterface $metadata = null,
        array $builders = [],
        array $decorators = [],
        private readonly TypeParser $parser = new TypeParser(),
        PropertyAccessorFactory $accessors = new PropertyAccessorFactory(),
        InstantiatorFactory $instantiators = new InstantiatorFactory(),
        array $hooks = [],
        Coercers $coercers = new Coercers(),
    ) {
        $this->repository = new TypeRepository(
            builders: [...$builders, ...self::createDefaultBuilders($this->parser)],
            metadata: $metadata ?? MetadataProviderFactory::create(),
            config: $config,
            decorators: $decorators,
            accessors: new ClassAccessors($accessors, $instantiators, $hooks),
            coercers: $coercers,
        );
    }

    /**
     * @return list<TypeBuilderInterface<mixed>>
     * @throws InvalidPatternException in case of a pattern is written wrong
     */
    public static function createDefaultBuilders(TypeParserInterface $parser = new TypeParser()): array
    {
        $string = new StringType();
        $int = new IntType();

        return [
            new NamedTypeBuilder(new NamedTypePattern(['int', 'integer'], $parser), $int),
            new NamedTypeBuilder(new NamedTypePattern(['float', 'double'], $parser), new FloatType()),
            new NamedTypeBuilder(new NamedTypePattern(['bool', 'boolean'], $parser), new BoolType()),
            new NamedTypeBuilder(new NamedTypePattern(['mixed'], $parser), new MixedType()),
            new NamedTypeBuilder(new NamedTypePattern(['string'], $parser), $string),
            new NamedTypeBuilder(
                new NamedTypePattern(['non-empty-string'], $parser),
                new RefinedType($string, [new NonEmptyCheck()], 'non-empty-string'),
            ),
            new NamedTypeBuilder(
                new NamedTypePattern(['numeric-string'], $parser),
                new RefinedType($string, [new NumericCheck()], 'numeric-string'),
            ),
            new IntRangeTypeBuilder(new NamedTypePattern(['int<min, max>'], $parser), $int),
            new NamedTypeBuilder(
                new NamedTypePattern(['positive-int'], $parser),
                new RefinedType($int, [new IntRangeCheck(min: 1)], 'positive-int'),
            ),
            new NamedTypeBuilder(
                new NamedTypePattern(['negative-int'], $parser),
                new RefinedType($int, [new IntRangeCheck(max: -1)], 'negative-int'),
            ),
            new NamedTypeBuilder(
                new NamedTypePattern(['non-positive-int'], $parser),
                new RefinedType($int, [new IntRangeCheck(max: 0)], 'non-positive-int'),
            ),
            new NamedTypeBuilder(
                new NamedTypePattern(['non-negative-int'], $parser),
                new RefinedType($int, [new IntRangeCheck(min: 0)], 'non-negative-int'),
            ),
            new NamedTypeBuilder(new NamedTypePattern(['array-key'], $parser), new ArrayKeyType()),
            new ListTypeBuilder(new NamedTypePattern(['list', 'list<T>'], $parser)),
            new ListTypeBuilder(
                new NamedTypePattern(['non-empty-list', 'non-empty-list<T>'], $parser),
                [new NonEmptyCheck()],
            ),
            new ArrayTypeBuilder(new NamedTypePattern(['array', 'array<V>', 'array<K, V>'], $parser)),
            new TypesListTypeBuilder(),
            new ArrayTypeBuilder(
                new NamedTypePattern(['non-empty-array', 'non-empty-array<V>', 'non-empty-array<K, V>'], $parser),
                [new NonEmptyCheck()],
            ),
            new IterableTypeBuilder(new NamedTypePattern(['iterable', 'iterable<V>', 'iterable<K, V>'], $parser)),
            new ShapeTypeBuilder(),
            new NullTypeBuilder(),
            new LiteralTypeBuilder(),
            new ConstantTypeBuilder(),
            new UnionTypeBuilder(),
            new BackedEnumTypeBuilder(),
            new UnitEnumTypeBuilder(),
            new ObjectTypeBuilder(),
            new DateTimeTypeBuilder(),
            new DiscriminatorTypeBuilder(),
            new ClassTypeBuilder(),
        ];
    }

    public function denormalize(mixed $value, #[Language('PHP')] string $type): mixed
    {
        return $this->getType($type)->denormalize($value, $this->repository->context);
    }

    public function isDenormalizable(mixed $value, #[Language('PHP')] string $type): bool
    {
        return $this->getType($type)->isDenormalizable($value, $this->repository->context);
    }

    /**
     * A value without the type is normalized by `mixed`: an object by the
     * type of its class, an array element by element.
     */
    public function normalize(mixed $value, #[Language('PHP')] ?string $type = null): mixed
    {
        return $this->getType($type ?? 'mixed')->normalize($value, $this->repository->context);
    }

    public function isNormalizable(mixed $value, #[Language('PHP')] ?string $type = null): bool
    {
        return $this->getType($type ?? 'mixed')->isNormalizable($value, $this->repository->context);
    }

    /**
     * @param non-empty-string $type
     * @return TypeInterface<mixed>
     * @throws TypeDefinitionException in case of the type is written wrong
     *         or cannot be built
     */
    private function getType(string $type): TypeInterface
    {
        if (isset($this->types[$type])) {
            return $this->types[$type];
        }

        try {
            /** @var TypeNode */
            $node = $this->parser->forType()->parse($type);
        } catch (ParserExceptionInterface $e) {
            throw TypeDefinitionException::becauseTypeIsWrittenWrong($type, $e);
        }

        return $this->types[$type] = $this->repository->getType($node);
    }
}
