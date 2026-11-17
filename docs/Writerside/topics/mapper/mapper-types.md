# Built-in Types

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

Every type string passed to `Mapper::denormalize()` or `Mapper::normalize()`
is parsed once and built from the list of builders returned by
`Mapper::createDefaultBuilders()`. This page lists everything that list
understands, grouped by kind.

Each type works in two directions:

- **denormalization** turns external data (decoded JSON, form input, a
  config array) into PHP values. Here the mapper is lenient: unless the
  mapper or the property is [strict](mapper-coercion.md#strict-mode), a
  value written in another form is [coerced](mapper-coercion.md) (`'42'`
  becomes `42` for `int`), and refined types check their condition;
- **normalization** turns PHP values back into data. Here the mapper is
  exact: a value must already be of the type, nothing is coerced, and the
  conditions of refined types are not checked.

A type name the builders do not recognise, or one that is written wrong,
fails with `TypeDefinitionException` when the type is first used. The syntax
of the type strings themselves is described in the
[type language](basic-types.md) section.

## Scalars {id="scalars"}

```php
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

$mapper = new Mapper();

var_dump($mapper->denormalize('42', 'int'));
var_dump($mapper->denormalize('1.5', 'float'));
var_dump($mapper->denormalize('off', 'bool'));
var_dump($mapper->denormalize('42', 'number'));

try {
    $mapper->normalize('42', 'int');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}
```

```
int(42)
float(1.5)
bool(false)
int(42)
$: Value must be of type "int", but "42" given
```

| Type                               | Denormalization                                                        | Normalization          |
|------------------------------------|------------------------------------------------------------------------|------------------------|
| `int`, `integer`, `literal-int`    | an integer; integer strings and integral floats are coerced            | an integer             |
| `float`, `double`, `real`          | a float, including `INF` and `NAN`; numeric strings and integers are coerced | a float          |
| `bool`, `boolean`                  | a boolean; `1`, `0`, `'true'`, `'off'` and the like are coerced        | a boolean              |
| `string`                           | a string; integers and finite floats are coerced                       | a string               |
| `mixed`                            | any value as is                                                        | see below              |
| `scalar`                           | `int`, `float`, `bool` or `string`                                     | the same               |
| `number`                           | `int` or `float`                                                       | the same               |
| `numeric`                          | `int`, `float` or `numeric-string`                                     | the same               |
| `array-key`                        | an integer or a string, never coerced                                  | an integer or a string |

The exact coercion rules of each scalar are listed in
[Coercion](mapper-coercion.md). `scalar`, `number` and `numeric` behave as
the [unions](#unions) of their members: `'42'` stays a string for `scalar`
and `numeric` (a string is an exact match) but becomes `42` for `number`.

`mixed` returns any data unchanged on denormalization. On normalization it
looks at the value: an object is normalized by the type of its own class,
an array and a `\stdClass` element by element, other values are kept. This
is also what `Mapper::normalize()` does when no type is given.

`literal-int` is accepted for compatibility with static analysers and means
plain `int`: whether the value comes from a literal cannot be checked at
runtime.

## Refined types {id="refinements"}

A refined type takes the value by its base type (with coercion) and then
checks a condition. The condition applies to **denormalization only**; on
normalization the value is checked by the base type alone.

```php
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

$mapper = new Mapper();

var_dump($mapper->denormalize('7', 'int<1, 10>'));

try {
    $mapper->denormalize('', 'non-empty-string');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}

// normalization takes the value by the base type, without the check
var_dump($mapper->normalize('', 'non-empty-string'));
var_dump($mapper->normalize(-5, 'positive-int'));
```

```
int(7)
$: Value must be of type "non-empty-string" (non-empty)
string(0) ""
int(-5)
```

### Strings {id="refined-strings"}

| Type                                             | Accepts                                                  | Rejects                     |
|--------------------------------------------------|----------------------------------------------------------|-----------------------------|
| `non-empty-string`, `non-empty-literal-string`   | any string but `''`                                      | `''`                        |
| `non-falsy-string`, `truthy-string`              | any string but `''` and `'0'`                            | `''`, `'0'`                 |
| `numeric-string`                                 | what `is_numeric()` accepts: `'42'`, `'-1.5'`, `'1e3'`, `' 42'` | `'abc'`, `'0x1A'`    |
| `decimal-int-string`                             | an integer in canonical decimal form: `'42'`, `'-5'`     | `'042'`, `'+5'`, `'1.0'`    |
| `non-decimal-int-string`                         | any string but such an integer: `'042'`, `'abc'`         | `'42'`                      |
| `lowercase-string`, `non-empty-lowercase-string` | a string without uppercase ASCII letters                 | `'Abc'`                     |
| `uppercase-string`, `non-empty-uppercase-string` | a string without lowercase ASCII letters                 | `'aBC'`                     |

The base type is `string`, so an integer is coerced first: `42` is accepted
by `non-empty-string` and `decimal-int-string` as `'42'`. The case checks
look at ASCII letters only.

The following names are accepted for compatibility with PHPStan and Psalm
annotations and are plain `string` at runtime: nothing is checked, a class
name is not resolved and the class does not have to exist.

- `literal-string`, `callable-string`;
- `class-string`, `class-string<T>`, `interface-string`,
  `interface-string<T>`, `trait-string`, `enum-string`, `enum-string<T>`.

`non-empty-literal-string` is checked as `non-empty-string` only.

### Integers {id="refined-integers"}

| Type               | Condition                             |
|--------------------|---------------------------------------|
| `positive-int`     | `>= 1`                                |
| `negative-int`     | `<= -1`                               |
| `non-positive-int` | `<= 0`                                |
| `non-negative-int` | `>= 0`                                |
| `non-zero-int`     | not `0`                               |
| `int<min, max>`    | between the bounds, both inclusive    |

Each bound of `int<min, max>` is an integer or the word `min` (lower) or
`max` (upper) for an open side: `int<1, 10>`, `int<0, max>`,
`int<min, -1>`. `int<min, max>` is plain `int`. A range whose lower bound is
greater than the upper one, or a bound that is neither, is a definition
error:

```
Type "int<10, 1>" is empty: the lower bound is greater than the upper one
Template argument #2 of type "int<1, foo>" is invalid: must be an integer or "max"
```

### Floats {id="refined-floats"}

| Type                 | Condition                                 |
|----------------------|-------------------------------------------|
| `positive-float`     | `> 0`                                     |
| `negative-float`     | `< 0`                                     |
| `non-positive-float` | `<= 0`                                    |
| `non-negative-float` | `>= 0`                                    |
| `finite-float`       | not `INF`, `-INF` or `NAN`                |

`NAN` fails every float condition. Plain `float` accepts `INF` and `NAN`.

### Other values {id="refined-other"}

| Type               | Base     | Condition                                                 |
|--------------------|----------|-----------------------------------------------------------|
| `non-empty-scalar` | `scalar` | not falsy: rejects `0`, `0.0`, `''`, `'0'`, `false`       |
| `empty-scalar`     | `scalar` | falsy: only `0`, `0.0`, `''`, `'0'`, `false`              |
| `non-empty-mixed`  | `mixed`  | not falsy: also rejects `null` and `[]`                   |
| `non-null-mixed`   | `mixed`  | anything but `null`                                       |

## Flags {id="int-mask"}

`int-mask<...>` and `int-mask-of<...>` describe an integer made of the given
bits only. `0` (no flags) is always accepted.

```php
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

final class Permission
{
    public const READ = 1;
    public const WRITE = 2;
    public const DELETE = 4;
}

$mapper = new Mapper();

var_dump($mapper->denormalize(3, 'int-mask<1, 2, 4>'));
var_dump($mapper->denormalize(5, 'int-mask-of<Permission::READ|Permission::DELETE>'));
var_dump($mapper->denormalize('7', 'int-mask-of<Permission::*>'));

try {
    $mapper->denormalize(8, 'int-mask-of<Permission::*>');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}
```

```
int(3)
int(5)
int(7)
$: Value must be of type "int-mask-of<Permission::*>" (bits of 7)
```

- `int-mask<A, B, ...>` takes any number of arguments;
- `int-mask-of<T>` takes exactly one.

Each argument is a non-negative integer, a [constant](#constants) holding
one, a union of them, or a mask of constants. An argument that is anything
else, such as a mask matching string constants (the `Order` class from
[Constants](#constants)), is a definition error:

```
Type "int-mask-of<Order::STATUS_*>" must be made of non-negative integers, their constants or unions of them
```

The base type is `int`, so `'7'` is coerced; as with other refined types the
bits are not checked on normalization.

## Literals {id="literals"}

A literal type accepts one value: `42`, `-1`, `1.5`, `'active'`, `true`,
`false`, `null`. See [literal types](literal-types.md) for the syntax.

The value is taken by the type of its kind, so other forms of the same value
are coerced unless the mapper is strict. Normalization requires exactly the
value.

| Type       | Input      | Result                                                   |
|------------|------------|----------------------------------------------------------|
| `42`       | `'42'`     | `42`                                                     |
| `42`       | `43`       | `Value must be of type "42", but 43 given`               |
| `'active'` | `'other'`  | `Value must be of type "'active'", but "other" given`    |
| `true`     | `'yes'`    | `true`                                                   |
| `false`    | `0`        | `false`                                                  |
| `null`     | `''`       | `Value must be of type "null", but "" given`             |

Literals are most useful in [unions](#unions): `'asc'|'desc'`.

## Constants {id="constants"}

A class constant `Foo::BAR` is the literal of its value. Its value must be a
scalar, `null` or a case of an enum; an enum case is denormalized from its
value or name as the [enum](#enums) itself. See
[constant types](const-types.md) for the syntax.

A mask selects several constants by a name pattern with `*` and is the union
of their values:

- `Foo::STATUS_*`, `Foo::*` - constants of a class or interface (for an
  enum `Foo::*` also matches its cases);
- `E_*` - global constants;
- `App\LIMIT_*` - constants of a namespace.

A single global or namespaced constant without `*` (`PHP_EOL`) is not
supported.

```php
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

final class Order
{
    public const STATUS_NEW = 'new';
    public const STATUS_PAID = 'paid';
    public const VERSION = 2;
}

$mapper = new Mapper();

var_dump($mapper->denormalize('2', 'Order::VERSION'));
var_dump($mapper->denormalize('paid', 'Order::STATUS_*'));
var_dump($mapper->denormalize(E_WARNING, 'E_*'));

try {
    $mapper->denormalize('deleted', 'Order::STATUS_*');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}
```

```
int(2)
string(4) "paid"
int(2)
$: Value must be of type "Order::STATUS_*", but "deleted" given
```

As in a union, the string values of a mask are tried last when coercing:
with `CODE_1 = 1` and `CODE_2 = '1'`, the input `1.0` becomes `1`, while
`'1'` stays `'1'`, being an exact match.

Definition errors, for the `Order` class above with an extra
`public const LIMITS = [1, 2]`:

```
Type "Order::MISSING" refers to no constant
Type "self::VERSION" refers to a class relative to the declaration, write the class by its name
Type "Order::LIMITS" refers to a constant of type "array", only a scalar, "null" or a case of an enum is allowed
```

## Collections {id="collections"}

| Type                                                         | Keys             | Elements |
|--------------------------------------------------------------|------------------|----------|
| `list`, `list<T>`                                            | `0, 1, 2, ...`   | `T`, `mixed` by default |
| `non-empty-list`, `non-empty-list<T>`                        | the same, at least one element | the same |
| `array`, `array<V>`, `array<K, V>`, `V[]`                    | `K`, `array-key` by default | `V`, `mixed` by default |
| `non-empty-array`, `non-empty-array<V>`, `non-empty-array<K, V>` | the same, at least one element | the same |
| `associative-array`, `non-empty-associative-array` (with the same arguments) | the same as `array` | the same as `array` |
| `iterable`, `iterable<V>`, `iterable<K, V>`                  | the same as `array` | the same as `array` |

```php
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

$mapper = new Mapper();

var_dump($mapper->denormalize(['1', '2'], 'list<int>'));
var_dump($mapper->denormalize(['a' => '1'], 'array<string, int>'));
var_dump($mapper->denormalize(new ArrayIterator(['a' => '1']), 'iterable<string, int>'));

try {
    $mapper->denormalize([1 => 'x'], 'list<string>');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}

try {
    $mapper->denormalize(['x' => 'y'], 'array<int, string>');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}
```

```
array(2) {
  [0]=>
  int(1)
  [1]=>
  int(2)
}
array(1) {
  ["a"]=>
  int(1)
}
array(1) {
  ["a"]=>
  int(1)
}
$: Value must be of type "list<string>", but array(1) given
$.x: Key must be of type "int", but "x" given
```

- A `list` requires the keys `0, 1, 2, ...` in order in both directions; an
  array with other keys is rejected, not renumbered.
- An `array` keeps its keys. Each key is checked by the key type and always
  [coerced](mapper-coercion.md#array-keys), even in strict mode.
- `associative-array` is a synonym of `array`: whether the keys are
  sequential is not checked.
- Every collection also accepts a `\stdClass` as input (a JSON object
  decoded without `true`), read as an array of its properties.
- `iterable` additionally accepts any `Traversable`, which is read into an
  array with its keys, in both directions. A generator can be read only
  once. The `array` and `list` types do not accept a `Traversable`.

By default the first invalid element stops the conversion; to collect more,
see [Errors](mapper-errors.md).

## Shapes {id="shapes"}

`array{...}` describes an array with known keys, `object{...}` the same
fields of an object. See [shape types](shape-types.md) for the syntax.

```php
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

$mapper = new Mapper();

$data = ['id' => '1', 'tags' => ['a'], 'debug' => true];

var_dump($mapper->denormalize($data, 'array{id: int, name?: string, tags: list<string>}'));
var_dump($mapper->denormalize($data, 'array{id: int, ...}'));
var_dump($mapper->denormalize(['1', 'x'], 'array{int, string}'));
var_dump($mapper->denormalize(['id' => '1'], 'object{id: int}'));

try {
    $mapper->denormalize(['name' => 'x'], 'array{id: int, name?: string}');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}
```

```
array(2) {
  ["id"]=>
  int(1)
  ["tags"]=>
  array(1) {
    [0]=>
    string(1) "a"
  }
}
array(3) {
  ["id"]=>
  int(1)
  ["tags"]=>
  array(1) {
    [0]=>
    string(1) "a"
  }
  ["debug"]=>
  bool(true)
}
array(2) {
  [0]=>
  int(1)
  [1]=>
  string(1) "x"
}
object(stdClass)#903 (1) {
  ["id"]=>
  int(1)
}
$.id: Required field "id" is missing
```

- **Keys.** A field is named (`id: int`), keyed by a literal (`'id': int`,
  `0: int`) or implicit (`array{int, string}`); an implicit field gets the
  next integer key, as in a PHP array literal. A key declared twice is a
  definition error.
- **Optional fields** (`name?: string`) may be absent in both directions;
  every other field is required.
- **Sealed shapes** (without `...`) drop the keys they do not declare, in
  both directions. With `Configuration(shouldRejectExtraFields: true)` an
  undeclared key is reported as `Unexpected field "debug"` on
  denormalization instead (see [Configuration](mapper-configuration.md)).
- **Unsealed shapes** (`array{id: int, ...}`) keep the other keys as they
  are, by `mixed`.
- **`object{...}`** gives a `\stdClass` on denormalization. On normalization
  it takes the public properties of any object and gives an array, or a
  `\stdClass` when `Configuration(isObjectAsArray: false)`.

### object and stdClass {id="object"}

`object` denormalizes an array or a `\stdClass` into a `\stdClass` with all
its fields. On normalization it accepts any object and normalizes it by the
type of its class, as [`mixed`](#scalars) does.

`stdClass` (or `\stdClass`) is an `object{...}` shape without declared
fields and unsealed: a `\stdClass` with all fields on denormalization, an
array (or a `\stdClass`, see above) of all its properties on normalization.

## Unions and nullable types {id="unions"}

A union `A|B|...` is resolved in two passes on denormalization:

1. **Exact match.** Each type is tried in the declared order without
   coercion; the first one that accepts the value as it is wins.
2. **Coercion.** Only if nothing matched and the mapper is not strict, the
   types are tried again with coercion. In this pass the string types
   (`string`, any `*-string` and string literals) go last, so a value is
   coerced to a string only when nothing else accepts it.

```php
use TypeLang\Mapper\Mapper;

$mapper = new Mapper();

// exact match: '42' already is a string
var_dump($mapper->denormalize('42', 'int|string'));

// coercion phase: 42.0 is no int and no string, int is tried before string
var_dump($mapper->denormalize(42.0, 'string|int'));

// declared order decides between coercible non-string types
var_dump($mapper->denormalize('1', 'int|bool'));
var_dump($mapper->denormalize('1', 'bool|int'));

var_dump($mapper->denormalize(null, '?int'));
var_dump($mapper->denormalize('5', '?int'));
```

```
string(2) "42"
int(42)
int(1)
bool(true)
NULL
int(5)
```

On normalization the first type in the declared order that accepts the
value is used.

`?T` and `T|null` accept `null` as is and any other value by `T`. An empty
string is not `null`: `''` for `?int` is an error. See
[logical types](logical-types.md) for the syntax.

## Enums {id="enums"}

A **backed enum** is denormalized from the value of a case and normalized to
it. The value is first taken by the backing type, so `'2'` finds the case
`2` of an `int` enum unless the mapper is strict.

Other values can be mapped to a case with the repeatable `#[MapAlias]`
attribute on the case, for example the former values of a renamed case. An
alias must be of the backing type, must not be the value of any case and
may belong to one case only; an invalid alias is a metadata error (see
[Metadata](mapper-metadata.md), where the `aliases` key of config files is
described too). A case is always normalized to its own value.

A **unit enum** (without values) is denormalized from the name of a case,
case-sensitive, and normalized to the name.

`value-of<E>` of a backed enum is the union of the values of its cases,
kept as scalars and not turned into cases. Aliases are not part of it.
`value-of` of a unit enum is not supported.

```php
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MapAlias;

enum Status: string
{
    #[MapAlias('enabled', 'on')]
    case Active = 'active';
    case Blocked = 'blocked';
}

enum Priority: int
{
    case Low = 1;
    case High = 2;
}

enum Suit
{
    case Hearts;
    case Spades;
}

$mapper = new Mapper();

var_dump($mapper->denormalize('enabled', 'Status'));
var_dump($mapper->normalize(Status::Active, 'Status'));
var_dump($mapper->denormalize('2', 'Priority'));
var_dump($mapper->denormalize('Spades', 'Suit'));
var_dump($mapper->normalize(Suit::Spades, 'Suit'));
var_dump($mapper->denormalize('2', 'value-of<Priority>'));

try {
    $mapper->denormalize('ACTIVE', 'Status');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}
```

```
enum(Status::Active)
string(6) "active"
enum(Priority::High)
enum(Suit::Spades)
string(6) "Spades"
int(2)
$: Value must be of type "Status", but "ACTIVE" given
```

## Dates {id="dates"}

`DateTimeInterface`, `DateTimeImmutable`, `DateTime` and their instantiable
subclasses are dates. `DateTimeInterface` is created as `DateTimeImmutable`.

- **Without a format** a string is parsed by the constructor of the class,
  so anything PHP understands is accepted (`'2024-05-01 10:30'`,
  `'tomorrow'`). The date is normalized to RFC 3339
  (`DateTimeInterface::RFC3339`, `Y-m-d\TH:i:sP`), without fractions of a
  second.
- **With a format** (`DateTimeImmutable<'Y-m-d'>`) the string must match it
  exactly: fields absent from the format are zero (a `!` is prepended unless
  the format has `!` or `|`), and an impossible date such as `2024-02-30` is
  rejected. The date is normalized by the same format. For Unix timestamps
  use `DateTimeImmutable<'U'>`.
- An empty string is never a date.
- An object of the type is accepted as is. A `DateTime` is not a
  `DateTimeImmutable` and vice versa; use `DateTimeInterface` to accept
  both.

```php
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

date_default_timezone_set('UTC');

$mapper = new Mapper();

$date = $mapper->denormalize('2024-05-01 10:30:00', 'DateTimeImmutable');
echo get_class($date), ' ', $date->format('Y-m-d H:i:s'), "\n";

$date = $mapper->denormalize('2024-05-01', "DateTimeImmutable<'Y-m-d'>");
echo get_class($date), ' ', $date->format('Y-m-d H:i:s'), "\n";

$date = $mapper->denormalize('2024-05-01T10:30:00+02:00', 'DateTimeInterface');
echo get_class($date), ' ', $date->format('Y-m-d H:i:s P'), "\n";

var_dump($mapper->normalize(new DateTimeImmutable('2024-05-01 10:30:00.250'), 'DateTimeImmutable'));
var_dump($mapper->normalize(new DateTimeImmutable('2024-05-01 10:30:00'), "DateTimeImmutable<'d.m.Y'>"));

foreach (['', '2024-02-30'] as $value) {
    try {
        $mapper->denormalize($value, "DateTimeImmutable<'Y-m-d'>");
    } catch (InvalidValueException $e) {
        echo $e->getMessage(), "\n";
    }
}
```

```
DateTimeImmutable 2024-05-01 10:30:00
DateTimeImmutable 2024-05-01 00:00:00
DateTimeImmutable 2024-05-01 10:30:00 +02:00
string(25) "2024-05-01T10:30:00+00:00"
string(10) "01.05.2024"
$: Value must be of type "DateTimeImmutable<'Y-m-d'>", but "" given
$: Value must be of type "DateTimeImmutable<'Y-m-d'>", but "2024-02-30" given
```

The format argument must be a single non-empty string literal:

```
Template argument #1 of type "DateTimeImmutable<'Y-m-d', 'x'>" is invalid: must be the only one, a non-empty string of the format
```

## Classes {id="classes"}

Any other name of a user class is the type of that class: on
denormalization an object is created and its properties are filled from an
array or a `\stdClass`; on normalization the object becomes an array (or a
`\stdClass`) of its properties. Which properties take part, their types,
external names, defaults and skipping rules come from the class
[metadata](mapper-metadata.md). Internal PHP classes other than those
described above are not supported. See [Mapping classes](mapper-classes.md).

## Polymorphic classes {id="polymorphism"}

A class or interface whose metadata declares a discriminator (for example
with `#[DiscriminatorMap]`) is polymorphic: on denormalization the value of
the tag field of the data selects the class to create, on normalization the
class of the object selects the type and the tag is added to the data. The
base may be abstract or an interface. See
[Polymorphism](mapper-polymorphism.md).
