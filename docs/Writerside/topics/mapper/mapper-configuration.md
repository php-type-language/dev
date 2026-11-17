# Configuration

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

Everything the mapper does is set up through the constructor of
`TypeLang\Mapper\Mapper`. Every argument is optional, `new Mapper()` is a
complete, ready to use mapper. Use named arguments to override only what
you need:

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;
use TypeLang\Mapper\Naming\SnakeCaseNamingStrategy;

final class Profile
{
    public string $firstName;
    public ?string $avatarUrl = null;
}

$mapper = new Mapper(
    config: new Configuration(shouldRejectExtraFields: true),
    metadata: MetadataProviderFactory::create(strategy: new SnakeCaseNamingStrategy()),
);

$profile = $mapper->denormalize(['first_name' => 'Alice'], Profile::class);

var_dump($mapper->normalize($profile));
```

```
array(2) {
  ["first_name"]=>
  string(5) "Alice"
  ["avatar_url"]=>
  NULL
}
```

## Constructor Arguments

| Argument        | Type                                 | Purpose                                                                                                                         |
|-----------------|--------------------------------------|---------------------------------------------------------------------------------------------------------------------------------|
| `config`        | `Configuration`                      | Conversion settings: strictness, object form, violations, extra fields. [Below](#configuration-options).                        |
| `metadata`      | `?MetadataProviderInterface`         | Where the mapping of classes is read from, `MetadataProviderFactory::create()` by default. [Metadata](mapper-metadata.md).       |
| `builders`      | `list<TypeBuilderInterface>`         | Your own types, tried before the built-in ones. [Extending](mapper-extending.md).                                              |
| `decorators`    | `list<TypeDecoratorInterface>`       | Wrap the built types, in the order of the list. [Extending](mapper-extending.md).                                              |
| `parser`        | `TypeParser`                         | Parses the type strings given to the mapper and the type names of the built-in builders. Types in class metadata are parsed by the parser of the metadata provider (`MetadataProviderFactory::create(parser: ...)`). |
| `accessors`     | `PropertyAccessorFactory`            | How properties are read and written. [Extending](mapper-extending.md).                                                         |
| `instantiators` | `InstantiatorFactory`                | How objects are created during denormalization. [Extending](mapper-extending.md).                                             |
| `hooks`         | `list<ClassHookInterface>`           | Services run before and after the conversion of objects of the classes they support. [Extending](mapper-extending.md).         |
| `coercers`      | `Coercers`                           | How a value written in another form becomes a value of a scalar type or an array. [Coercion](mapper-coercion.md).              |

The built-in types are returned by the static `Mapper::createDefaultBuilders()`.
The `builders` you pass are put in front of them, so a builder of your own
can take over a type name the mapper already knows.

## Configuration Options

`TypeLang\Mapper\Context\Configuration` holds the settings of the
conversion itself:

| Option                    | Default | Effect                                                                                   |
|---------------------------|---------|------------------------------------------------------------------------------------------|
| `isStrict`                | `false` | A value must match the type exactly, without coercion.                                   |
| `isObjectAsArray`         | `true`  | An object is normalized to an array, `false` gives a `stdClass`.                          |
| `maxViolations`           | `1`     | Number of violations a denormalization collects before it fails.                         |
| `shouldRejectExtraFields` | `false` | A field of the data no property or sealed shape key takes is a violation, not dropped.  |

`Configuration` is immutable. `withStrictTypes(bool)` returns a copy with
only the strictness changed.

### Strict Mode

By default a scalar written in another form is coerced: the string `'10'`
becomes the `int` `10`. With `isStrict: true` such a value is rejected:

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

final class Point
{
    public int $x;
    public int $y;
}

$data = ['x' => '10', 'y' => 20];

print_r((new Mapper())->denormalize($data, Point::class));

try {
    (new Mapper(new Configuration(isStrict: true)))->denormalize($data, Point::class);
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}
```

```
Point Object
(
    [x] => 10
    [y] => 20
)
$.x: Value must be of type "int", but "10" given
```

Which values are coerced, and how to override strictness for a single
property with `#[MapType(..., strict: ...)]`, is described in
[Coercion](mapper-coercion.md).

### Objects as Arrays or stdClass

Normalization turns an object into an array by default. An array without
keys and an object without properties both become `[]` and are encoded by
`json_encode()` as `[]`. With `isObjectAsArray: false` objects become
`stdClass` instances, and an empty one stays `{}` in JSON:

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Mapper;

final class Options {}

final class Settings
{
    public string $theme = 'dark';
    public Options $options;

    public function __construct()
    {
        $this->options = new Options();
    }
}

$settings = new Settings();

echo \json_encode((new Mapper())->normalize($settings)), "\n";

$mapper = new Mapper(new Configuration(isObjectAsArray: false));

echo \json_encode($mapper->normalize($settings)), "\n";
var_dump($mapper->normalize($settings) instanceof \stdClass);
```

```
{"theme":"dark","options":[]}
{"theme":"dark","options":{}}
bool(true)
```

A class can choose its own form regardless of this option, see
[Classes](mapper-classes.md).

### Collecting Violations

The first invalid value stops a denormalization by default. A larger
`maxViolations` lets it go on and collect up to that number of violations
before it fails:

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

foreach ([1, 2, 100] as $max) {
    try {
        (new Mapper(new Configuration(maxViolations: $max)))
            ->denormalize(['a', 'b', 'c'], 'list<int>');
    } catch (InvalidValueException $e) {
        echo \count($e->violations), ': ', $e->getMessage(), "\n";
    }
}
```

```
1: $[0]: Value must be of type "int", but "a" given
2: $[0]: Value must be of type "int", but "a" given (and 1 more)
3: $[0]: Value must be of type "int", but "a" given (and 2 more)
```

Collecting is useful for forms and API requests, where the client should
see every problem at once. How to read the collected violations is
described in [Errors](mapper-errors.md).

### Extra Fields

A field of the data that no property of a class takes is dropped by
default. With `shouldRejectExtraFields: true` it is a violation:

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

final class Point
{
    public int $x;
    public int $y;
}

$data = ['x' => 1, 'y' => 2, 'z' => 3];

print_r((new Mapper())->denormalize($data, Point::class));

try {
    (new Mapper(new Configuration(shouldRejectExtraFields: true)))->denormalize($data, Point::class);
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}
```

```
Point Object
(
    [x] => 1
    [y] => 2
)
$.z: Unexpected field "z"
```

The option also applies to sealed shapes such as `array{a: int}`. An
unsealed shape, `array{a: int, ...}`, keeps the extra fields in any mode.

## Reusing the Mapper

A `Mapper` parses every type string once, builds a converter for it once,
reads the mapping of every class once and keeps all of that for the
following calls. Creating it is therefore far more expensive than a
conversion: on a development machine with PHP 8.4, `new Mapper()` took
about 1.5 ms of CPU, while denormalizing or normalizing a small class with
a nested object took about 2 µs once its type was built.

Create one mapper per configuration and share it, such as a service of your
DI container, instead of creating one per request or per call.
