# Extending

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

## Custom Types

A type implements `TypeInterface`: it checks and converts a value in both
directions.

```php
use TypeLang\Mapper\Context\Context;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Type\TypeInterface;

final class Money
{
    public function __construct(
        public readonly int $cents,
        public readonly string $currency,
    ) {}
}

/**
 * @template-implements TypeInterface<Money>
 */
final class MoneyType implements TypeInterface
{
    public function isDenormalizable(mixed $value, Context $context): bool
    {
        return \is_string($value)
            && \preg_match('/^\d+\.\d{2} [A-Z]{3}$/', $value) === 1;
    }

    public function denormalize(mixed $value, Context $context): Money
    {
        if (!$this->isDenormalizable($value, $context)) {
            throw InvalidValueException::becauseInvalidType($value);
        }

        [$amount, $currency] = \explode(' ', $value);

        return new Money((int) \str_replace('.', '', $amount), $currency);
    }

    public function isNormalizable(mixed $value, Context $context): bool
    {
        return $value instanceof Money;
    }

    public function normalize(mixed $value, Context $context): string
    {
        if (!$value instanceof Money) {
            throw InvalidValueException::becauseInvalidType($value);
        }

        return \sprintf(
            '%d.%02d %s',
            \intdiv($value->cents, 100),
            $value->cents % 100,
            $value->currency,
        );
    }
}
```

Register it for a name with `NamedTypeBuilder`:

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Type\Builder\NamedTypeBuilder;
use TypeLang\Mapper\TypeMatcher\NamedTypePattern;

$mapper = new Mapper(builders: [
    new NamedTypeBuilder(new NamedTypePattern(['money']), new MoneyType()),
]);

$mapper->denormalize('10.50 USD', 'money');
// Money { cents: 1050, currency: 'USD' }

$mapper->normalize(new Money(1050, 'USD'), 'money'); // '10.50 USD'
$mapper->denormalize('ten dollars', 'money');
// throws InvalidValueException: $: Value must be of type money, but "ten
// dollars" given

$mapper->denormalize(['10.50 USD'], 'list<money>');
// [Money { cents: 1050, currency: 'USD' }]
```

<note>
Builders of the user are tried before the built-in ones, so a builder can
also replace a built-in type. Only that name is replaced: refined types built
on it, such as <code>positive-int</code> for <code>int</code>, keep the
built-in type.
</note>

## Type Builders

A builder creates a type from the parsed type node. Use it for a type with
arguments, such as `collection<T>`.

```php
use TypeLang\Mapper\Context\BuildContext;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Type\Builder\TypeBuilderInterface;
use TypeLang\Mapper\Type\TypeInterface;
use TypeLang\Mapper\TypeMatcher\NamedTypePattern;
use TypeLang\Type\TypeNode;

final class Collection
{
    public function __construct(public readonly array $items) {}
}

final class CollectionTypeBuilder implements TypeBuilderInterface
{
    private readonly NamedTypePattern $pattern;

    public function __construct()
    {
        $this->pattern = new NamedTypePattern(['collection<T>']);
    }

    public function tryBuild(
        TypeNode $node,
        BuildContext $context,
    ): ?TypeInterface {
        $arguments = $this->pattern->findArguments($node);

        if ($arguments === null) {
            return null;
        }

        // the type of the items, built by the mapper
        $items = $context->types->getType($arguments['T']);

        return new CollectionType($items);
    }
}

$mapper = new Mapper(builders: [new CollectionTypeBuilder()]);

$mapper->denormalize(['1', '2'], 'collection<int>');
// Collection { items: [1, 2] }
```

An error of an item, wrapped by your type with the key of the item
(`InvalidValueException::wrapWithPath()`), is shown without the expected
type (`$[1]: Value "x" is invalid`), unless the builder tells the type of
its items with `CompositeTypeBuilderInterface`:

```php
use TypeLang\Mapper\Type\Builder\CompositeTypeBuilderInterface;

final class CollectionTypeBuilder implements CompositeTypeBuilderInterface
{
    // ...

    public function tryGetNestedType(
        TypeNode $node,
        int|string $key,
        bool $isKey,
    ): ?TypeNode {
        if ($isKey) {
            return null;
        }

        return $this->pattern->findArguments($node)['T'] ?? null;
    }
}

$mapper->denormalize(['1', 'x'], 'collection<int>');
// throws InvalidValueException: $[1]: Value must be of type int, but "x"
// given
```

## Type Decorators

A decorator wraps every built type, such as for logging:

```php
use TypeLang\Mapper\Context\BuildContext;
use TypeLang\Mapper\Type\Decorator\TypeDecoratorInterface;
use TypeLang\Mapper\Type\TypeInterface;
use TypeLang\Type\TypeNode;

final class LoggingDecorator implements TypeDecoratorInterface
{
    public function decorate(
        TypeInterface $type,
        TypeNode $node,
        BuildContext $context,
    ): TypeInterface {
        // a TypeInterface of your own around the built type
        return new LoggingType($type, $context->printer->print($node));
    }
}

$mapper = new Mapper(decorators: [new LoggingDecorator()]);
```

<warning>
<code>int-mask&lt;...&gt;</code> and <code>int-mask-of&lt;...&gt;</code> fail to build
when the types of their members are wrapped: return the types of integer
literals, constants and their unions as is.
</warning>

## Class Hooks

A hook runs before or after the conversion of objects of the classes it
supports:

| Interface                           | Method                 |
|-------------------------------------|------------------------|
| `BeforeDenormalizationHookInterface`| `beforeDenormalize()` — changes the data |
| `AfterDenormalizationHookInterface` | `afterDenormalize()` — checks or completes the object |
| `BeforeNormalizationHookInterface`  | `beforeNormalize()` — changes the object |
| `AfterNormalizationHookInterface`   | `afterNormalize()` — changes the data |

```php
use TypeLang\Mapper\Context\Context;
use TypeLang\Mapper\Hook\AfterDenormalizationHookInterface;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;

final class Period
{
    public int $from;
    public int $to;
}

final class PeriodHook implements AfterDenormalizationHookInterface
{
    public function isSupported(ClassMetadata $metadata): bool
    {
        return $metadata->name === Period::class;
    }

    public function afterDenormalize(
        ClassMetadata $metadata,
        object $object,
        mixed $data,
        Context $context,
    ): void {
        if ($object->from > $object->to) {
            throw new \DomainException('The period ends before it starts');
        }
    }
}

$mapper = new Mapper(hooks: [new PeriodHook()]);

$mapper->denormalize(['from' => 5, 'to' => 1], Period::class);
// throws InvalidValueException: $: The period ends before it starts
```

The exception of a hook becomes a violation of the object.

## Instantiation

An instantiation strategy creates the objects of a class. By default an
object is created without its constructor.

```php
use TypeLang\Mapper\Instantiator\InstantiatorInterface;
use TypeLang\Mapper\Instantiator\Strategy\InstantiationStrategyInterface;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;

final class EntityStrategy implements InstantiationStrategyInterface
{
    public function tryCreateInstantiator(
        \ReflectionClass $class,
        ClassMetadata $metadata,
    ): ?InstantiatorInterface {
        if (!$class->implementsInterface(Entity::class)) {
            return null;
        }

        // ... an instantiator taking the entity by "id" of the data
    }
}

$mapper = new Mapper(instantiators: [new EntityStrategy()]);
```

<warning>
All mapped properties of the created object are written, missing fields get
their default values.
</warning>

<note>
The choice of a branch of a union does not call the instantiator: in
`Entity|int` the data of an unknown entity selects `Entity` and fails.
</note>

## Property Access

An access strategy reads and writes properties. The built-in strategies
write directly, through the backing value of a PHP 8.4 hook, or by
reflection for private and readonly properties.

```php
use TypeLang\Mapper\Mapper;

$mapper = new Mapper(accessors: [new MyAccessStrategy()]);
```

A strategy implements `AccessStrategyInterface` and returns `null` for
properties it does not support.
