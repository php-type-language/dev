# Coercion

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

External data rarely has the exact PHP types: query strings and form fields
are all strings, some JSON producers write `42.0` for an integer, a
checkbox sends `'on'`. **Coercion** lets the mapper accept such a value when
it is the same value written in another form: `'42'` for `int`, `42.0` for
`int`, `'true'` for `bool`.

Coercion follows a few rules:

- it applies to **denormalization** only and is enabled by default;
- the value must not change its meaning or lose precision: `'42.5'` is not
  an `int`, and `9007199254740993` is not a `float`;
- a string is read as a whole: no trimming, no trailing garbage, no PHP
  source syntax such as `0x1A` or `1_000`;
- `null` is never coerced, it is handled by [nullable types](mapper-types.md#unions);
- objects are never turned into scalars: a `Stringable`, an enum case or a
  date is rejected by `string`.

Every type built on a scalar coerces the same way: [refined
types](mapper-types.md#refinements), [literals](mapper-types.md#literals),
the values of [backed enums](mapper-types.md#enums), the strings of
[dates](mapper-types.md#dates) and the second pass of
[unions](mapper-types.md#unions).

## int {id="int"}

| Input                                    | Result                  |
|------------------------------------------|-------------------------|
| `'42'`, `'+42'`                          | `42`                    |
| `'007'`                                  | `7`                     |
| `'-0'`                                   | `0`                     |
| `'9223372036854775807'`                  | `PHP_INT_MAX`           |
| `'42.0'`, `'42.'`                        | `42`                    |
| `42.0`                                   | `42`                    |
| `-0.0`                                   | `0`                     |
| `1e15`                                   | `1000000000000000`      |

Rejected: `'9223372036854775808'` (overflow), `'42.5'`, `'1e3'`, `' 42'`,
`"42\n"`, `'42abc'`, `'0x1A'`, `'1_000'`, `''`, `42.5`, `INF`, `NAN`,
`true`, `false`.

## float {id="float"}

| Input                     | Result     |
|---------------------------|------------|
| `'1.5'`, `'+1.5'`         | `1.5`      |
| `'42'`                    | `42.0`     |
| `'-0'`                    | `-0.0`     |
| `'1e3'`                   | `1000.0`   |
| `'.5'`, `'5.'`            | `0.5`, `5.0` |
| `42`                      | `42.0`     |
| `9007199254740992` (2<sup>53</sup>) | `9007199254740992.0` |

Rejected: `9007199254740993`, `PHP_INT_MAX` and `'9007199254740993'` (no
exact `float`), `'1e400'` (not finite), `'1e-400'` (underflows to zero),
`'INF'`, `'NAN'`, `' 1.5'`, `'0x1A'`, `''`, `true`.

A string with a fraction or an exponent is rounded to the nearest `float`,
as for any decimal notation: `'0.1'` has no exact binary form either.

## string {id="string"}

| Input          | Result                    |
|----------------|---------------------------|
| `42`           | `'42'`                    |
| `1.5`          | `'1.5'`                   |
| `42.0`         | `'42'`                    |
| `-0.0`         | `'-0'`                    |
| `0.1 + 0.2`    | `'0.30000000000000004'`   |
| `1e25`         | `'1.0e+25'`               |

A float is written in the shortest form that reads back to the same number,
as `json_encode()` writes it, regardless of the `serialize_precision` ini
setting.

Rejected: `INF`, `-INF`, `NAN`, `true`, `false`.

## bool {id="bool"}

| Input                                              | Result  |
|----------------------------------------------------|---------|
| `1`, `'1'`, `'true'`, `'yes'`, `'on'`              | `true`  |
| `0`, `'0'`, `'false'`, `'no'`, `'off'`             | `false` |

The words are case-insensitive: `'TRUE'`, `'Off'`.

Rejected: `2`, `-1`, `1.0`, `''`, `' true'`, `'y'`, `'t'`, `'2'`.

## Arrays {id="arrays"}

Arrays, lists, shapes and classes read their input as an array. Besides an
array they accept a `\stdClass`, the object `json_decode()` returns for a
JSON object, read as an array of its properties. This applies in strict mode
too.

Rejected: subclasses of `\stdClass`, other objects (including
`ArrayObject`), scalars. A `Traversable` is accepted by
[`iterable`](mapper-types.md#collections) only.

## Values rejected by every type {id="rejected"}

`null`, objects (a `Stringable`, an enum case, a date) and arrays are never
coerced to `int`, `float`, `string` or `bool`. For example, `null` given for
`int` fails with:

```
$: Value must be of type "int", but null given
```

## Strict mode {id="strict-mode"}

`Configuration(isStrict: true)` turns coercion off for the whole mapper: a
scalar must already have its type.

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

$mapper = new Mapper();
$strict = new Mapper(new Configuration(isStrict: true));

var_dump($mapper->denormalize('42', 'int'));

try {
    $strict->denormalize('42', 'int');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}

// an integer that has an exact float is still a float
var_dump($strict->denormalize(42, 'float'));

// a JSON object is still an array
var_dump($strict->denormalize((object) ['id' => 1], 'array{id: int}'));
```

```
int(42)
$: Value must be of type "int", but "42" given
float(42)
array(1) {
  ["id"]=>
  int(1)
}
```

Even in strict mode:

- `float` accepts an integer that has an exact `float` (JSON does not
  distinguish `42` from `42.0`);
- a `\stdClass` is accepted where an array is expected;
- [array keys](#array-keys) are coerced.

A union in strict mode only looks for an exact match, its coercion pass is
skipped.

### Per property {id="per-property"}

The second argument of `#[MapType]` overrides the strictness for one
property: `strict: true` makes it strict in a lenient mapper,
`strict: false` lenient in a strict one, and `null` (the default) follows
the mapper configuration. The setting covers the whole value of the
property, including the elements of a collection.

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MapType;

final class Payment
{
    #[MapType('int', strict: true)]
    public int $amount;

    public int $quantity;

    #[MapType('string', strict: false)]
    public string $reference;
}

$mapper = new Mapper();

try {
    $mapper->denormalize(['amount' => '100', 'quantity' => '2', 'reference' => 1], Payment::class);
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}

$strict = new Mapper(new Configuration(isStrict: true));

var_dump($strict->denormalize(['amount' => 100, 'quantity' => 2, 'reference' => 77], Payment::class));
```

```
$.amount: Value must be of type "int", but "100" given
object(Payment)#1822 (3) {
  ["amount"]=>
  int(100)
  ["quantity"]=>
  int(2)
  ["reference"]=>
  string(2) "77"
}
```

Config files set the same with the `strict` key of a property, see
[Metadata](mapper-metadata.md).

## Normalization never coerces {id="normalization"}

Normalization takes PHP values that are expected to be of the declared type
already. A value of another type is an error in any mode, so a wrong type
in your own objects is reported instead of being silently converted.

```php
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

$mapper = new Mapper();

var_dump($mapper->denormalize(['1', '2'], 'list<int>'));

try {
    $mapper->normalize(['1', '2'], 'list<int>');
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
$[0]: Value must be of type "int", but "1" given
```

## Array keys {id="array-keys"}

The keys of `array<K, V>` are always checked by `K` with coercion, even in
strict mode. PHP itself turns the key `"10"` into the integer `10`, so a
JSON object with numeric keys could not pass `array<string, V>` otherwise.
The elements still follow the strictness of the mapper.

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

$strict = new Mapper(new Configuration(isStrict: true));

// json_decode() turns the key "10" into the integer 10
$data = json_decode('{"10": 1, "20": 2}', true);

var_dump($strict->denormalize($data, 'array<string, int>'));

try {
    $strict->denormalize(['10' => '1'], 'array<string, int>');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
}
```

```
array(2) {
  [10]=>
  int(1)
  [20]=>
  int(2)
}
$[10]: Value must be of type "int", but "1" given
```

The keys of the result are still integers: a PHP array cannot hold the
string `'10'` as a key.

## Custom coercers {id="custom-coercers"}

The coercion rules of a mapper are a `Coercers` object passed to its
constructor. It holds one coercer per target: `int`, `float`, `string`,
`bool` and `array`; replace any of them by name, the others keep their
defaults.

A coercer implements `CoercerInterface`:

```php
namespace TypeLang\Mapper\Coercer;

/**
 * @template-covariant T of int|float|string|bool|array<array-key, mixed>
 */
interface CoercerInterface
{
    /**
     * @return T|null
     */
    public function coerce(mixed $value): mixed;
}
```

`coerce()` returns the converted value, or `null` when the value cannot be
converted. A value that already has the target type is returned as is. It
must not throw: a rejection is reported by the type that called it.

The example below adds decimal commas to `float` and leaves everything else
to the default `FloatCoercer`:

```php
use TypeLang\Mapper\Coercer\CoercerInterface;
use TypeLang\Mapper\Coercer\FloatCoercer;
use TypeLang\Mapper\Context\Coercers;
use TypeLang\Mapper\Mapper;

/**
 * Reads "1,5" as 1.5, everything else as the default coercer does.
 *
 * @template-implements CoercerInterface<float>
 */
final class DecimalCommaCoercer implements CoercerInterface
{
    public function __construct(
        private readonly CoercerInterface $default = new FloatCoercer(),
    ) {}

    public function coerce(mixed $value): ?float
    {
        if (is_string($value) && substr_count($value, ',') === 1 && !str_contains($value, '.')) {
            $value = str_replace(',', '.', $value);
        }

        return $this->default->coerce($value);
    }
}

$mapper = new Mapper(coercers: new Coercers(float: new DecimalCommaCoercer()));

var_dump($mapper->denormalize('1,5', 'float'));
var_dump($mapper->denormalize('2.25', 'float'));
var_dump($mapper->denormalize(['price' => '19,99'], 'array{price: positive-float}'));
```

```
float(1.5)
float(2.25)
array(1) {
  ["price"]=>
  float(19.99)
}
```

The replaced coercer applies wherever the type is used, including refined
types such as `positive-float` above.

<note>
The <code>int</code>, <code>string</code> and <code>bool</code> coercers are
called only when the mapper or the property is not strict. The
<code>float</code> coercer is also called for integers in strict mode, and
the <code>array</code> coercer reads the input of arrays, lists, shapes and
classes in any mode.
</note>
