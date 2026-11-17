# Extending

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

Everything the mapper converts goes through a **type**: an object that turns
data into a PHP value and back. Types are created by **builders** from the
parsed type string, optionally wrapped by **decorators**, and classes are
converted with the help of **hooks**, **instantiation strategies** and
**property access strategies**. Each of these is a small interface you can
implement and pass to the `Mapper` constructor:

| Extension point                                       | Constructor argument |
|-------------------------------------------------------|----------------------|
| [Custom types](#custom-types) and [builders](#type-builders) | `builders`    |
| [Decorators](#decorators)                             | `decorators`         |
| [Class hooks](#class-hooks)                           | `hooks`              |
| [Instantiation strategies](#instantiation-strategies) | `instantiators`      |
| [Property access strategies](#property-access-strategies) | `accessors`      |

Where the description of a class comes from (attributes, config files,
readers of your own) is covered in [](mapper-metadata.md).

## Custom Types

A type implements `TypeLang\Mapper\Type\TypeInterface`, four methods, two per
direction:

| Method                                        | Direction       | Contract                                                  |
|-----------------------------------------------|-----------------|-----------------------------------------------------------|
| `isDenormalizable(mixed $value, Context $context): bool` | data to PHP | answers whether `denormalize()` would succeed, never throws on data |
| `denormalize(mixed $value, Context $context): mixed`     | data to PHP | returns the PHP value or throws                          |
| `isNormalizable(mixed $value, Context $context): bool`   | PHP to data | answers whether `normalize()` would succeed, never throws on data   |
| `normalize(mixed $value, Context $context): mixed`       | PHP to data | returns the data or throws                               |

The checks must agree with the conversions: `isDenormalizable($x)` is `true`
exactly when `denormalize($x)` succeeds. The mapper relies on this, for
example a union type picks the first branch whose check says `true`, and
`Mapper::isDenormalizable()` calls the check only.

A value that cannot be converted is reported by throwing
`InvalidValueException::becauseInvalidType($expected, $value)`, where
`$expected` is the name of the type shown in the message. Any other
`\Exception` works too and becomes a violation with its message, but only
`becauseInvalidType()` produces the standard `InvalidType` violation (see
[](mapper-errors.md)). The enclosing types (lists, shapes, classes) add the
path of the value to the error. An `\Error` is a bug, not invalid data, and
passes through as is.

A type is built once and shared by every conversion, so it must not keep
state between calls. Everything that depends on the call comes in the
`Context`:

| Member                             | What for                                                                       |
|------------------------------------|--------------------------------------------------------------------------------|
| `$context->config`                 | the [configuration](mapper-configuration.md): `isStrict`, `maxViolations`, ... |
| `$context->coercers`               | the [coercers](mapper-coercion.md) the built-in scalar types use outside strict mode |
| `$context->types->get($class)`     | the type of a class met at runtime, for example of an object in `mixed`         |
| `$context->withStrictTypes($bool)` | the same context with the other strictness, to convert a nested value          |

### Registering a Type

A type without arguments is registered for one or more names with
`NamedTypeBuilder` and `NamedTypePattern`. Builders passed to the mapper are
tried before the default ones, so a name of your own can also replace a
built-in type.

Here a `Money` value object is mapped from a string like `"10.50 USD"`:

```php
use TypeLang\Mapper\Context\Context;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MapType;
use TypeLang\Mapper\Type\Builder\NamedTypeBuilder;
use TypeLang\Mapper\Type\TypeInterface;
use TypeLang\Mapper\TypeMatcher\NamedTypePattern;

final class Money
{
    public function __construct(
        public readonly string $amount,
        public readonly string $currency,
    ) {}
}

/**
 * @template-implements TypeInterface<Money>
 */
final class MoneyType implements TypeInterface
{
    private const PATTERN = '/^(\d+\.\d{2}) ([A-Z]{3})$/';

    public function isDenormalizable(mixed $value, Context $context): bool
    {
        return \is_string($value) && \preg_match(self::PATTERN, $value) === 1;
    }

    public function denormalize(mixed $value, Context $context): Money
    {
        if (!\is_string($value) || \preg_match(self::PATTERN, $value, $matches) !== 1) {
            throw InvalidValueException::becauseInvalidType('money-string', $value);
        }

        return new Money($matches[1], $matches[2]);
    }

    public function isNormalizable(mixed $value, Context $context): bool
    {
        return $value instanceof Money;
    }

    public function normalize(mixed $value, Context $context): string
    {
        if (!$value instanceof Money) {
            throw InvalidValueException::becauseInvalidType('money-string', $value);
        }

        return $value->amount . ' ' . $value->currency;
    }
}

final class Product
{
    public string $title;

    #[MapType('money-string')]
    public Money $price;
}

$mapper = new Mapper(builders: [
    new NamedTypeBuilder(new NamedTypePattern(['money-string']), new MoneyType()),
]);

$product = $mapper->denormalize(['title' => 'Book', 'price' => '10.50 USD'], Product::class);

var_dump($product->price);
var_dump($mapper->normalize($product));
var_dump($mapper->isDenormalizable('ten dollars', 'money-string'));

try {
    $mapper->denormalize(['3.00 EUR', '10 USD'], 'list<money-string>');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}
```

```
object(Money)#1165 (2) {
  ["amount"]=>
  string(5) "10.50"
  ["currency"]=>
  string(3) "USD"
}
array(2) {
  ["title"]=>
  string(4) "Book"
  ["price"]=>
  string(9) "10.50 USD"
}
bool(false)
$[1]: Value must be of type "money-string", but "10 USD" given
```

Once registered, the name works everywhere a type does: as an argument of
`denormalize()`, inside `list<money-string>`, in `#[MapType]`, PHPDoc and
config files.

`NamedTypePattern` lists the forms a name may be written in, such as
`['int', 'integer']` or `['list', 'list<T>']`. The match ignores the case of
the name; a form with arguments captures them by name, which builders read
with `$pattern->findArguments($node)`. A pattern matches simple names only,
not class names.

<warning>
Inside a namespace, a simple name in <code>#[MapType]</code> or PHPDoc is
first resolved to a class of that namespace if one exists, ignoring case.
With a class <code>App\Money</code>, <code>#[MapType('money')]</code> in
<code>namespace App</code> means that class, and the registered type is never
asked. Pick names that cannot clash with a class, such as
<code>money-string</code>.
</warning>

## Type Builders

`NamedTypeBuilder` returns the same type for every match. A type that depends
on its arguments, or that is chosen by a class name, needs a builder of its
own: `TypeBuilderInterface` with one method,
`tryBuild(TypeNode $node, BuildContext $context): ?TypeInterface`.

- The builder receives the parsed type, a `TypeLang\Type\TypeNode` (see the
  [type language](basic-types.md)), and returns `null` if the node is not its
  own. The first builder returning a type wins.
- Nested types are taken from `$context->types->getType($node)`, which
  builds them by all the builders, including yours. A type that refers to
  itself while being built gets a reference, builders do not handle
  recursion.
- A node the builder takes but that is written wrong, such as an argument out
  of range, is reported by throwing `TypeDefinitionException`.
- `BuildContext` also gives the metadata provider (`$context->metadata`) and a
  printer of type nodes (`$context->printer->print($node)`), handy for the
  name of the type in messages.

Builders run once per distinct type string; the built type is reused.

This builder maps a `Collection<T>` class by converting it as `list<T>`:

```php
use TypeLang\Mapper\Context\BuildContext;
use TypeLang\Mapper\Context\Context;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Type\Builder\TypeBuilderInterface;
use TypeLang\Mapper\Type\TypeInterface;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\TypeNode;

/**
 * @template T
 */
final class Collection
{
    /**
     * @param list<T> $items
     */
    public function __construct(
        public readonly array $items,
    ) {}
}

/**
 * @template-implements TypeInterface<Collection<mixed>>
 */
final class CollectionType implements TypeInterface
{
    public function __construct(
        /**
         * @var TypeInterface<list<mixed>>
         */
        private readonly TypeInterface $list,
    ) {}

    public function isDenormalizable(mixed $value, Context $context): bool
    {
        return $this->list->isDenormalizable($value, $context);
    }

    public function denormalize(mixed $value, Context $context): Collection
    {
        return new Collection($this->list->denormalize($value, $context));
    }

    public function isNormalizable(mixed $value, Context $context): bool
    {
        return $value instanceof Collection
            && $this->list->isNormalizable($value->items, $context);
    }

    public function normalize(mixed $value, Context $context): array
    {
        if (!$value instanceof Collection) {
            throw InvalidValueException::becauseInvalidType('Collection', $value);
        }

        return $this->list->normalize($value->items, $context);
    }
}

/**
 * @template-implements TypeBuilderInterface<Collection<mixed>>
 */
final class CollectionTypeBuilder implements TypeBuilderInterface
{
    public function tryBuild(TypeNode $node, BuildContext $context): ?CollectionType
    {
        if (!$node instanceof NamedTypeNode
            || \strcasecmp(\ltrim($node->name->toString(), '\\'), Collection::class) !== 0
        ) {
            return null;
        }

        // Collection<T> is converted as list<T>, Collection as list
        $list = new NamedTypeNode(new Name('list'), $node->arguments);

        return new CollectionType($context->types->getType($list));
    }
}

final class Team
{
    /**
     * @var Collection<non-empty-string>
     */
    public Collection $members;
}

$mapper = new Mapper(builders: [new CollectionTypeBuilder()]);

$team = $mapper->denormalize(['members' => ['alice', 'bob']], Team::class);

var_dump($team->members);
var_dump($mapper->normalize($team));

try {
    $mapper->denormalize(['members' => ['alice', '']], Team::class);
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}

try {
    $mapper->denormalize([1, 2, 'x'], 'Collection<int>');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}
```

```
object(Collection)#895 (1) {
  ["items"]=>
  array(2) {
    [0]=>
    string(5) "alice"
    [1]=>
    string(3) "bob"
  }
}
array(1) {
  ["members"]=>
  array(2) {
    [0]=>
    string(5) "alice"
    [1]=>
    string(3) "bob"
  }
}
$.members[1]: Value must be of type "non-empty-string" (non-empty)
$[2]: Value must be of type "int", but "x" given
```

The builder only wraps `list<T>`: element types, coercion, error paths and
violation collection all come from the built-in list type. Since builders of
the user come first, the class `Collection` is no longer mapped property by
property.

## Decorators

A decorator (`TypeDecoratorInterface`) wraps types after they are built,
whatever builder made them. It receives the built type, its node and the
`BuildContext`, and returns either the type unchanged or a wrapper. It is
applied once per type, and the mapper keeps the result.

This one trims every string before it is checked:

```php
use TypeLang\Mapper\Context\BuildContext;
use TypeLang\Mapper\Context\Context;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Type\Decorator\TypeDecoratorInterface;
use TypeLang\Mapper\Type\StringType;
use TypeLang\Mapper\Type\TypeInterface;
use TypeLang\Type\TypeNode;

/**
 * @template-implements TypeInterface<string>
 */
final class TrimmingType implements TypeInterface
{
    public function __construct(
        /**
         * @var TypeInterface<string>
         */
        private readonly TypeInterface $delegate,
    ) {}

    public function isDenormalizable(mixed $value, Context $context): bool
    {
        return $this->delegate->isDenormalizable(self::trim($value), $context);
    }

    public function denormalize(mixed $value, Context $context): string
    {
        return $this->delegate->denormalize(self::trim($value), $context);
    }

    public function isNormalizable(mixed $value, Context $context): bool
    {
        return $this->delegate->isNormalizable($value, $context);
    }

    public function normalize(mixed $value, Context $context): mixed
    {
        return $this->delegate->normalize($value, $context);
    }

    private static function trim(mixed $value): mixed
    {
        return \is_string($value) ? \trim($value) : $value;
    }
}

final class TrimStringsDecorator implements TypeDecoratorInterface
{
    public function decorate(TypeInterface $type, TypeNode $node, BuildContext $context): TypeInterface
    {
        if (!$type instanceof StringType) {
            return $type;
        }

        return new TrimmingType($type);
    }
}

final class Contact
{
    public string $name;

    public string $email;
}

$mapper = new Mapper(decorators: [new TrimStringsDecorator()]);

var_dump($mapper->denormalize(['name' => '  Jane ', 'email' => "jane@example.com\n"], Contact::class));
var_dump($mapper->denormalize(['  a  '], 'list<string>'));
var_dump($mapper->denormalize('  b  ', 'non-empty-string'));
```

```
object(Contact)#897 (2) {
  ["name"]=>
  string(4) "Jane"
  ["email"]=>
  string(16) "jane@example.com"
}
array(1) {
  [0]=>
  string(1) "a"
}
string(5) "  b  "
```

Note the last line: a decorator sees the type built for a node, not the
types inside it. `non-empty-string` is built as a refinement around its own
string type, which never passes through the decorator, so the decorator
above does not see it. To cover such types, choose by the node (its name)
rather than by the class of the built type. Decorators are applied in the
order of the list, each one wrapping the result of the previous one.

## Class Hooks

Hooks run around the conversion of objects: to migrate an outdated payload,
to check invariants, to fill derived properties, to add links to the output.
A hook is a service implementing `ClassHookInterface::isSupported()` plus one
or more of the points:

| Interface                             | Method                                                  | Runs                                                 |
|---------------------------------------|---------------------------------------------------------|------------------------------------------------------|
| `BeforeDenormalizationHookInterface`  | `beforeDenormalize($metadata, $data, $context): mixed`  | before the object is created, returns the data to use |
| `AfterDenormalizationHookInterface`   | `afterDenormalize($metadata, $object, $data, $context): void` | after all properties are written                 |
| `BeforeNormalizationHookInterface`    | `beforeNormalize($metadata, $object, $context): object` | before the properties are read, returns the object to read |
| `AfterNormalizationHookInterface`     | `afterNormalize($metadata, $object, $data, $context): array` | after the properties are read, returns the data |

All interfaces live in `TypeLang\Mapper\Hook`. `isSupported()` receives the
class metadata and is asked once per class, so a class without hooks pays
nothing per object. Hooks of one class run in the order of the `hooks:` list.

An `\Exception` thrown by a hook becomes a violation of the object, with the
exception message as the text and the path of the object. An
`InvalidValueException` thrown by a hook is kept as is.

```php
use TypeLang\Mapper\Context\Context;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Hook\AfterDenormalizationHookInterface;
use TypeLang\Mapper\Hook\BeforeDenormalizationHookInterface;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;

final class Period
{
    public \DateTimeImmutable $from;

    public \DateTimeImmutable $to;

    public int $days = 0;
}

final class PeriodHook implements
    BeforeDenormalizationHookInterface,
    AfterDenormalizationHookInterface
{
    public function isSupported(ClassMetadata $metadata): bool
    {
        return $metadata->name === Period::class;
    }

    // {"start": ..., "end": ...} is the outdated form of the payload
    public function beforeDenormalize(ClassMetadata $metadata, mixed $data, Context $context): mixed
    {
        if (\is_array($data) && isset($data['start'], $data['end'])) {
            return ['from' => $data['start'], 'to' => $data['end']];
        }

        return $data;
    }

    public function afterDenormalize(ClassMetadata $metadata, object $object, mixed $data, Context $context): void
    {
        \assert($object instanceof Period);

        if ($object->to < $object->from) {
            throw new \DomainException('The period ends before it starts');
        }

        $object->days = $object->from->diff($object->to)->days;
    }
}

$mapper = new Mapper(hooks: [new PeriodHook()]);

$period = $mapper->denormalize(['start' => '2026-01-01', 'end' => '2026-01-31'], Period::class);

echo $period->days, "\n";

try {
    $mapper->denormalize(['from' => '2026-02-01', 'to' => '2026-01-01'], Period::class);
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}

try {
    $mapper->denormalize(['a' => ['from' => '2026-02-01', 'to' => '2026-01-01']], 'array<string, Period>');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}

var_dump($mapper->isDenormalizable(['from' => '2026-02-01', 'to' => '2026-01-01'], Period::class));
```

```
30
$: The period ends before it starts
$.a: The period ends before it starts
bool(true)
```

The last line shows the difference between the points:

- The "before" hooks also run inside the checks (`isDenormalizable()`,
  `isNormalizable()`), possibly several times for one value. They must have
  no side effects and depend on their arguments only.
- `afterDenormalize()` needs a created object, which the check does not
  create, so its exceptions are seen by `denormalize()` only.

<warning>
The message of any exception a hook throws ends up in the violation, and
from there possibly in an API response. Throw exceptions whose messages are
meant for the client of the data.
</warning>

## Instantiation Strategies

Before the properties are written, the mapper needs an object. By default it
is created without calling the constructor (the constructor is never called;
see [](mapper-classes.md)). An instantiation strategy changes that for some
classes: it implements `InstantiationStrategyInterface::tryCreateInstantiator()`,
returning `null` for classes it does not handle or an `InstantiatorInterface`
whose `instantiate(array $data): object` returns the object. Strategies are
given to an `InstantiatorFactory`, tried in the order of the list, before the
default one; the choice is made once per class.

`instantiate()` receives the data of the object (after the "before" hooks),
which makes it possible to take an existing object instead of a new one,
for example an entity by its identifier:

```php
use TypeLang\Mapper\Instantiator\InstantiatorFactory;
use TypeLang\Mapper\Instantiator\InstantiatorInterface;
use TypeLang\Mapper\Instantiator\Strategy\InstantiationStrategyInterface;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;

final class User
{
    public int $id;

    public string $name;

    public string $passwordHash = '';
}

final class UserRepository
{
    /**
     * @var array<int, User>
     */
    public array $users = [];

    public function find(int $id): ?User
    {
        return $this->users[$id] ?? null;
    }
}

/**
 * @template-implements InstantiatorInterface<User>
 */
final class UserInstantiator implements InstantiatorInterface
{
    public function __construct(
        private readonly UserRepository $users,
    ) {}

    public function instantiate(array $data = []): User
    {
        $id = $data['id'] ?? null;

        if (\is_int($id) && ($user = $this->users->find($id)) !== null) {
            return $user;
        }

        return new User();
    }
}

final class UserStrategy implements InstantiationStrategyInterface
{
    public function __construct(
        private readonly UserRepository $users,
    ) {}

    public function tryCreateInstantiator(\ReflectionClass $class, ClassMetadata $metadata): ?InstantiatorInterface
    {
        if ($class->name !== User::class) {
            return null;
        }

        return new UserInstantiator($this->users);
    }
}

$stored = new User();
$stored->id = 42;
$stored->name = 'Jane';
$stored->passwordHash = '$2y$10$...';

$users = new UserRepository();
$users->users[42] = $stored;

$mapper = new Mapper(instantiators: new InstantiatorFactory([new UserStrategy($users)]));

$user = $mapper->denormalize(['id' => 42, 'name' => 'Jane Doe'], User::class);

var_dump($user === $stored);
var_dump($user);
```

```
bool(true)
object(User)#2 (3) {
  ["id"]=>
  int(42)
  ["name"]=>
  string(8) "Jane Doe"
  ["passwordHash"]=>
  string(0) ""
}
```

<warning>
After <code>instantiate()</code> the mapper writes <b>every</b> mapped
property: from the data, or the default value when the field is missing. In
the output above <code>passwordHash</code> of the stored entity was reset to
its default <code>''</code>. A property that must survive has to be left out
of the mapping (for example by making it non-public). For the same reason an
instantiator cannot call a named constructor of a class with
<code>readonly</code> properties: writing them again fails with
<code>Error: Cannot modify readonly property</code>.
</warning>

## Property Access Strategies

Each mapped property gets a reader (for normalization) and a writer (for
denormalization), chosen once per property by a `PropertyAccessorFactory`.
The default strategies, in order:

| Strategy                   | Takes                                                                                            |
|----------------------------|--------------------------------------------------------------------------------------------------|
| `RawAccessStrategy`        | a property with a backing value and a hook of that direction: bypasses the hook (PHP 8.4+)       |
| `DirectAccessStrategy`     | reads a public property as `$object->name` (a virtual one through its `get` hook), writes a property writable from outside without a `set` hook |
| `ReflectionAccessStrategy` | anything else through reflection, regardless of visibility; a virtual property through its hook |

A static property, or a virtual property without the hook of the needed
direction, is rejected. Writing does not check or convert the value: it was
already converted by the type of the property.

Your own strategies implement `AccessStrategyInterface` with
`tryCreateReader()` and `tryCreateWriter()`, each returning `null` when the
strategy does not handle the property; a strategy may handle one direction
only. They are tried before the default ones. This strategy goes through
getters and setters, so the setter's logic runs on denormalization. Since
the default metadata maps public properties only, a
[property selector](mapper-metadata.md#sources-and-priority) is added to map
the private one:

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\Metadata\PropertyMetadata;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;
use TypeLang\Mapper\Mapping\Reader\Reflection\PropertySelectorInterface;
use TypeLang\Mapper\PropertyAccessor\PropertyAccessorFactory;
use TypeLang\Mapper\PropertyAccessor\PropertyReaderInterface;
use TypeLang\Mapper\PropertyAccessor\PropertyWriterInterface;
use TypeLang\Mapper\PropertyAccessor\Strategy\AccessStrategyInterface;

/**
 * @template-implements PropertyReaderInterface<object, mixed>
 */
final class GetterReader implements PropertyReaderInterface
{
    public function __construct(
        private readonly string $method,
    ) {}

    public function read(object $object): mixed
    {
        return $object->{$this->method}();
    }
}

/**
 * @template-implements PropertyWriterInterface<object, mixed>
 */
final class SetterWriter implements PropertyWriterInterface
{
    public function __construct(
        private readonly string $method,
    ) {}

    public function write(object $object, mixed $value): void
    {
        $object->{$this->method}($value);
    }
}

final class GetterSetterStrategy implements AccessStrategyInterface
{
    public function tryCreateReader(\ReflectionProperty $property, PropertyMetadata $metadata): ?PropertyReaderInterface
    {
        $method = 'get' . \ucfirst($property->name);

        if (!\method_exists($property->class, $method)) {
            return null;
        }

        return new GetterReader($method);
    }

    public function tryCreateWriter(\ReflectionProperty $property, PropertyMetadata $metadata): ?PropertyWriterInterface
    {
        $method = 'set' . \ucfirst($property->name);

        if (!\method_exists($property->class, $method)) {
            return null;
        }

        return new SetterWriter($method);
    }
}

// maps private properties too, the default selector takes public ones only
final class AllPropertiesSelector implements PropertySelectorInterface
{
    public function isSelected(\ReflectionProperty $property): bool
    {
        return !$property->isStatic();
    }
}

final class Customer
{
    private string $email = '';

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = \strtolower($email);
    }
}

$mapper = new Mapper(
    metadata: MetadataProviderFactory::create(selector: new AllPropertiesSelector()),
    accessors: new PropertyAccessorFactory([new GetterSetterStrategy()]),
);

$customer = $mapper->denormalize(['email' => 'Jane@Example.COM'], Customer::class);

var_dump($customer->getEmail());
var_dump($mapper->normalize($customer));
```

```
string(16) "jane@example.com"
array(1) {
  ["email"]=>
  string(16) "jane@example.com"
}
```

A reader may let an `\Error` escape for a property that cannot be read (an
uninitialized one); the mapper reports it as a violation of that property.
