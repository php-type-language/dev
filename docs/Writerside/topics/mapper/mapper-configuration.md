# Configuration

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

Configuration is a `TypeLang\Mapper\Context\Configuration` DTO passed to the
`TypeLang\Mapper\Mapper` constructor.

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Mapper;

$mapper = new Mapper(
    config: new Configuration(
        // options = true|false
        // default = false
        isStrict: false,

        // options = true|false
        // default = true
        isObjectAsArray: true,

        // options = int<1, max>
        // default = 1
        maxViolations: 1,

        // options = true|false
        // default = false
        shouldRejectExtraFields: false,
    ),
);
```

<tip>
Create the mapper once and reuse it (for example, as a service of your
container): it parses and builds every type only once.
</tip>

## Strict Types

Disables [type coercion](mapper-coercion.md) on denormalization: `'42'` is
no longer an `int`.

```php
$mapper = new Mapper(new Configuration(isStrict: true));

$mapper->denormalize(42, 'int');                               // 42
$mapper->denormalize('42', 'int');
// throws InvalidValueException: $: Value must be of type "int", but "42" given

$mapper->denormalize(42, 'float');                             // 42.0
$mapper->denormalize((object) ['id' => 42], 'array{id: int}'); // ['id' => 42]
```

<note>
Even in strict mode an <code>int</code> is accepted as a <code>float</code>,
a <code>stdClass</code> as an array, and the keys of arrays are converted.
A single property can override the option with
<a href="mapper-metadata-types.md#strict-types">metadata</a>.
</note>

## Object As Array

Whether objects are normalized into associative arrays or into
`stdClass`.

```php
final class User
{
    public string $name = 'Alice';
}

$mapper = new Mapper(new Configuration(isObjectAsArray: false));

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
// throws InvalidValueException: $[0]: Value must be of type "int", but "a"
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

$mapper = new Mapper(new Configuration(shouldRejectExtraFields: true));

$mapper->denormalize(['name' => 'Alice', 'age' => 42], User::class);
// throws InvalidValueException: $.age: Unexpected field "age"
```

<note>
The field of a <a href="mapper-metadata-discriminator.md">discriminator</a>
is not an extra field.
</note>

## Other Arguments

```php
use TypeLang\Mapper\Context\Coercers;
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Instantiator\InstantiatorFactory;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;
use TypeLang\Mapper\PropertyAccessor\PropertyAccessorFactory;
use TypeLang\Parser\TypeParser;

$mapper = new Mapper(
    config: new Configuration(),
    metadata: MetadataProviderFactory::create(),
    builders: [/* TypeBuilderInterface */],
    decorators: [/* TypeDecoratorInterface */],
    parser: new TypeParser(),
    accessors: new PropertyAccessorFactory([
        /* AccessStrategyInterface */
    ]),
    instantiators: new InstantiatorFactory([
        /* InstantiationStrategyInterface */
    ]),
    hooks: [/* ClassHookInterface */],
    coercers: new Coercers(),
);
```

- `metadata` — sources of the class mapping, see [metadata](mapper-metadata.md)
- `parser` — the parser of the type strings
- `builders`, `decorators`, `accessors`, `instantiators`, `hooks` — see
  [extending](mapper-extending.md)
- `coercers` — see [type coercion](mapper-coercion.md#custom-coercers)
