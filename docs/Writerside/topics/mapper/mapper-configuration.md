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

Disables [type coercion](mapper-coercion.md): a value must already have the
type.

```php
(new Mapper())->denormalize('42', 'int');                                  // 42
(new Mapper(new Configuration(isStrict: true)))->denormalize('42', 'int'); // throws InvalidValueException: $: Value must be of type "int", but "42" given
```

<note>
A single property can be made strict or non-strict with
<a href="mapper-metadata-options.md#strict-types">metadata</a>.
</note>

## Object As Array

Whether objects are normalized into associative arrays or into
`stdClass`.

```php
(new Mapper())->normalize(new User());                                          // ['name' => 'Alice']
(new Mapper(new Configuration(isObjectAsArray: false)))->normalize(new User()); // (object) ['name' => 'Alice']
```

<note>
A single class can override it with
<a href="mapper-metadata-options.md#normalize-as-array">metadata</a>.
</note>

## Max Violations

How many errors are collected before denormalization fails. By default it
fails on the first one.

```php
$mapper->denormalize(['a', 2, 'c'], 'list<int>'); // throws InvalidValueException: $[0]: Value must be of type "int", but "a" given (and 1 more)
```

## Reject Extra Fields

Rejects fields that no property of a class (or field of a sealed shape)
takes. By default they are ignored.

```php
$mapper->denormalize(['name' => 'Alice', 'age' => 42], User::class); // throws InvalidValueException: $.age: Unexpected field "age"
```

## Other Arguments

```php
use TypeLang\Mapper\Mapper;

$mapper = new Mapper(
    config: new Configuration(),
    metadata: MetadataProviderFactory::create(...),
    builders: [/* TypeBuilderInterface */],
    decorators: [/* TypeDecoratorInterface */],
    hooks: [/* ClassHookInterface */],
    accessors: new PropertyAccessorFactory([/* AccessStrategyInterface */]),
    instantiators: new InstantiatorFactory([/* InstantiationStrategyInterface */]),
    coercers: new Coercers(/* CoercerInterface */),
);
```

- `metadata` — sources of the class mapping, see [metadata](mapper-metadata.md)
- `builders`, `decorators`, `hooks`, `accessors`, `instantiators` — see
  [extending](mapper-extending.md)
- `coercers` — see [type coercion](mapper-coercion.md#custom-coercers)
