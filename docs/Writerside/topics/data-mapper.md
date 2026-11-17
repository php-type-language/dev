# Type Mapper

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

The mapper converts values by a TypeLang type in two directions:

- **Denormalization** turns external data, such as decoded JSON, a form or a
  row of a query, into PHP values of the type: objects of your classes,
  enums, dates, typed lists. The data is checked on the way, and every value
  that does not fit is reported with the path to it.
- **Normalization** goes back: it turns PHP values into plain data (scalars,
  arrays, `null`) ready for `json_encode()` or any other encoder.

The type is any [TypeLang type](introduction.md) written as a string:
a class name, `list<User>`, `array{id: int, tags?: list<string>}`,
`int|"auto"`. The mapper parses it once, builds a converter for it once and
reuses it for every following call.

## Installation

<tldr>
    <p>
        Via <a href="https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies">Composer</a>:
        <code lang="bash">composer require type-lang/mapper</code>
    </p>
</tldr>

**Requirements:**
* `PHP >= 8.1`

Optional packages enable additional features:

| Package                       | Enables                                                                     |
|-------------------------------|-----------------------------------------------------------------------------|
| `type-lang/phpdoc`            | Reading property types from PHPDoc (`@var list<User>`)                      |
| `symfony/yaml`                | Reading mapping configuration from YAML files                               |
| `nette/neon`                  | Reading mapping configuration from NEON files                               |
| `ext-json`                    | Reading mapping configuration from JSON files                               |
| `psr/cache`                   | Caching metadata in a PSR-6 pool                                            |
| `psr/simple-cache`            | Caching metadata in a PSR-16 cache                                          |
| `symfony/expression-language` | Skipping properties by an expression                                        |
| `phpstan/phpstan`             | Checking the syntax of type strings in mapper calls and attributes, see [PHPStan](mapper-phpstan.md) |

## Quick Start

A class is mapped by its public properties. Their native types are used as
is, a type PHP cannot express (here a list of strings) is given with the
`#[MapType]` attribute or, with `type-lang/phpdoc` installed, with a
`@var` tag:

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MapType;

final class Address
{
    public string $city;
    public string $street;
}

final class User
{
    public int $id;
    public string $name;
    public Address $address;

    #[MapType('list<string>')]
    public array $tags = [];
}

$mapper = new Mapper();

$data = \json_decode('{
    "id": 42,
    "name": "Alice",
    "address": {"city": "Paris", "street": "Rue de Rivoli"},
    "tags": ["admin", "editor"]
}', true);

$user = $mapper->denormalize($data, User::class);

print_r($user);

echo \json_encode($mapper->normalize($user), \JSON_PRETTY_PRINT), "\n";
```

```
User Object
(
    [id] => 42
    [name] => Alice
    [address] => Address Object
        (
            [city] => Paris
            [street] => Rue de Rivoli
        )

    [tags] => Array
        (
            [0] => admin
            [1] => editor
        )

)
{
    "id": 42,
    "name": "Alice",
    "address": {
        "city": "Paris",
        "street": "Rue de Rivoli"
    },
    "tags": [
        "admin",
        "editor"
    ]
}
```

The nested `Address` is denormalized by the type of its property, without
any extra declaration. Properties, names, defaults and everything else a
class can declare are described in [Classes](mapper-classes.md).

<tip>
Create the <code>Mapper</code> once and reuse it, such as a service of your
DI container: building it and the types it converts by costs much more than
a conversion itself. See <a href="mapper-configuration.md">Configuration</a>.
</tip>

## Type Strings

`denormalize()` requires the type, `normalize()` takes it optionally. The
type is not limited to a class name: any type of the
[language](introduction.md) the mapper knows is accepted, the full list is
in [Types](mapper-types.md).

```php
use TypeLang\Mapper\Mapper;

final class User
{
    public int $id;
    public string $name;
}

$mapper = new Mapper();

// a list of objects
$users = $mapper->denormalize([
    ['id' => 1, 'name' => 'Alice'],
    ['id' => 2, 'name' => 'Bob'],
], 'list<User>');

var_dump(\count($users), $users[1]->name);

// a shape
var_dump($mapper->denormalize(
    ['page' => '2', 'size' => 50],
    'array{page: positive-int, size?: int<1, 100>}',
));

// a union
var_dump($mapper->denormalize('auto', 'int|"auto"'));
var_dump($mapper->denormalize(10, 'int|"auto"'));

// normalization without a type infers it from the value
var_dump($mapper->normalize(['user' => $users[0], 'at' => new \DateTimeImmutable('2026-01-02 03:04:05+00:00')]));
```

```
int(2)
string(3) "Bob"
array(2) {
  ["page"]=>
  int(2)
  ["size"]=>
  int(50)
}
string(4) "auto"
int(10)
array(2) {
  ["user"]=>
  array(2) {
    ["id"]=>
    int(1)
    ["name"]=>
    string(5) "Alice"
  }
  ["at"]=>
  string(25) "2026-01-02T03:04:05+00:00"
}
```

Note the `'2'` of `page`: by default a value written in another form, such
as a numeric string of an `int`, is coerced. The rules and the strict mode
that turns them off are described in [Coercion](mapper-coercion.md).

Without a type, `normalize()` converts the value by `mixed`: an object by
the type of its class, an array element by element. Pass the type when the
output must follow a declared contract rather than the runtime value, such
as an interface or a narrower shape.

Class names in a type string are fully qualified: the string has no `use`
imports to resolve them against.

## Checking Without Converting

`isDenormalizable()` and `isNormalizable()` take the same arguments and
return whether the conversion would succeed, without building the result
and without throwing on invalid data:

```php
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

final class User
{
    public int $id;
    public string $name;
}

$mapper = new Mapper();

var_dump($mapper->isDenormalizable(['id' => 1, 'name' => 'Alice'], User::class));
var_dump($mapper->isDenormalizable(['id' => 'one', 'name' => 'Alice'], User::class));
var_dump($mapper->isNormalizable(new User(), 'list<User>'));

try {
    $mapper->denormalize(['id' => 'one', 'name' => 'Alice'], User::class);
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}
```

```
bool(true)
bool(false)
bool(false)
$.id: Value must be of type "int", but "one" given
```

A type string written wrong is still an error of your code, not of the
data: the predicates throw `TypeDefinitionException` for it as well. A
check creates no objects, so [hooks](mapper-extending.md) that run after an
object is filled are not run by `isDenormalizable()`, and a `true` does not
guarantee that such a hook accepts the object.

## Errors

Data that does not match the type fails with `InvalidValueException`, its
message starts with the JSONPath of the wrong value (`$.id` above) and its
`violations` list every problem found. A type that is unknown or written
wrong fails with `TypeDefinitionException`. Both implement
`MapperExceptionInterface`. Details are in [Errors](mapper-errors.md).

## Where to Go Next

- [Configuration](mapper-configuration.md): the `Mapper` constructor and the
  conversion settings.
- [Types](mapper-types.md): every built-in type and how it converts values.
- [Coercion](mapper-coercion.md): which values are accepted in another
  form, the strict mode, custom coercers.
- [Classes](mapper-classes.md): mapping objects: properties, types, names,
  defaults, skipping, readonly properties.
- [Polymorphism](mapper-polymorphism.md): choosing a class by a
  discriminator field.
- [Naming](mapper-naming.md): naming strategies of the external field names.
- [Metadata](mapper-metadata.md): where the mapping is read from: attributes,
  PHPDoc, configuration files, caching.
- [Errors](mapper-errors.md): exceptions, violations, paths and messages.
- [Extending](mapper-extending.md): custom types, builders, decorators,
  hooks, instantiation and property access.
- [PHPStan](mapper-phpstan.md): checking type strings during static analysis.
