# Configuration

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

Configuration is a `TypeLang\Mapper\Configuration` DTO passed to the
`TypeLang\Mapper\Mapper` constructor.

```php
use TypeLang\Mapper\Configuration;
use TypeLang\Mapper\Mapper;

$mapper = new Mapper(
    config: new Configuration(
        // options = true|false
        // default = false
        strict: false,

        // options = true|false
        // default = true
        normalizeAsArray: true,

        // options = int<1, max>
        // default = 1
        maxViolations: 1,

        // options = true|false
        // default = false
        rejectExtraFields: false,

        // options = int<0, max>
        // default = 1
        expectedTypeDepth: 1,

        // options = int<1, max>|null
        // default = null
        expectedTypeMaxFields: null,
    ),
);
```

<tip>
Create the mapper once and reuse it (for example, as a service of your
container): it parses and builds every type only once.
</tip>

## Strict

Disables [type coercion](mapper-coercion.md) on denormalization: `'42'` is
no longer an `int`.

```php
$mapper = new Mapper(new Configuration(strict: true));

$mapper->denormalize(42, 'int');                               // 42
$mapper->denormalize('42', 'int');
// throws InvalidValueException: $: Value must be of type int, but "42" given

$mapper->denormalize(42, 'float');                             // 42.0
$mapper->denormalize((object) ['id' => 42], 'array{id: int}'); // ['id' => 42]
```

<note>
Even in strict mode an <code>int</code> is accepted as a <code>float</code>,
a <code>stdClass</code> as an array, and the keys of arrays are converted.
A single property can override the option with
<a href="mapper-metadata-types.md#strict-types">metadata</a>.
</note>

## Normalize As Array

Whether objects are normalized into associative arrays or into
`stdClass`.

```php
final class User
{
    public string $name = 'Alice';
}

$mapper = new Mapper(new Configuration(normalizeAsArray: false));

$mapper->normalize(new User()); // (object) ['name' => 'Alice']
```

<note>
A single class can override it with
<a href="mapper-metadata-misc.md#normalize-as-array">metadata</a>.
</note>

## Max Violations

How many errors are collected before denormalization fails. By default it
fails on the first one. Normalization always fails on the first one.

```php
$mapper = new Mapper(new Configuration(maxViolations: 100));

$mapper->denormalize(['a', 2, 'c'], 'list<int>');
// throws InvalidValueException: $[0]: Value must be of type int, but "a"
// given (and 1 more)
```

See [collecting violations](mapper-errors.md#collecting-violations) to read
all of them.

## Reject Extra Fields

Rejects fields that no property of a class (or field of a sealed shape)
takes. By default they are ignored.

```php
final class User
{
    public string $name;
}

$mapper = new Mapper(new Configuration(rejectExtraFields: true));

$mapper->denormalize(['name' => 'Alice', 'age' => 42], User::class);
// throws InvalidValueException: $.age: Unexpected field "age"
```

<note>
The field of a <a href="mapper-metadata-discriminator.md">discriminator</a>
is not an extra field.
</note>

## Expected Type Depth

How many levels of classes the [messages](mapper-errors.md#messages) describe
by their fields. By default `1`: the fields of a class, its nested classes
are `object`. `0` describes a class as `object`.

```php
final class User
{
    public string $name;
    public ?Address $address = null;
}

final class Address
{
    public string $city;
}

$mapper = new Mapper(new Configuration(expectedTypeDepth: 0));

$mapper->denormalize('x', User::class);
// throws InvalidValueException: $: Value must be of type object, but "x" given

$mapper = new Mapper();

$mapper->denormalize('x', User::class);
// throws InvalidValueException: $: Value must be of type object{name: string,
// address?: ?object}, but "x" given

$mapper = new Mapper(new Configuration(expectedTypeDepth: 2));

$mapper->denormalize('x', User::class);
// throws InvalidValueException: $: Value must be of type object{name: string,
// address?: ?object{city: string}}, but "x" given
```

## Expected Type Max Fields

How many fields of a class the messages show, the others are `...`. By
default all of them.

```php
$mapper = new Mapper(new Configuration(expectedTypeMaxFields: 1));

$mapper->denormalize('x', User::class);
// throws InvalidValueException: $: Value must be of type object{name: string,
// ...}, but "x" given
```

## Other Arguments

```php
use TypeLang\Mapper\Configuration;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MetadataBuilder;
use TypeLang\Parser\TypeParser;
use TypeLang\Printer\PrettyTypePrinter;

$mapper = new Mapper(
    config: new Configuration(),
    builders: [/* TypeBuilderInterface */],
    hooks: [/* ClassHookInterface */],
    decorators: [/* TypeDecoratorInterface */],
    expanders: [/* TypeExpanderInterface */],
    accessors: [/* AccessStrategyInterface */],
    instantiators: [/* InstantiationStrategyInterface */],
    coercers: [/* 'int' => CoercerInterface, ... */],
    metadata: MetadataBuilder::createDefault()->build(),
    printer: new PrettyTypePrinter(),
    parser: new TypeParser(),
);
```

- `builders`, `hooks`, `decorators`, `accessors`, `instantiators` — see
  [extending](mapper-extending.md), they are tried before the built-in ones
- `expanders` — expand the types of your own in the messages of errors, see
  [errors](mapper-errors.md), tried before the built-in ones
- `coercers` — replace the built-in coercers by the type: `int`, `float`,
  `string`, `bool`, `array`, see
  [type coercion](mapper-coercion.md#custom-coercers)
- `metadata` — sources of the class mapping, see [metadata](mapper-metadata.md)
- `printer` — prints types in errors
- `parser` — the parser of the type strings
