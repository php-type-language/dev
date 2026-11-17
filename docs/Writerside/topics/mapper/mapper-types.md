# Types

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

Types of the [language](introduction.md) the mapper converts. Values given in
another form, such as `'42'` for `int`, are converted by the
[type coercion](mapper-coercion.md) rules.

## Scalars

| Type                  | Aliases                       |
|-----------------------|-------------------------------|
| `int`                 | `integer`, `literal-int`      |
| `float`               | `double`, `real`              |
| `bool`                | `boolean`                     |
| `string`              |                               |
| `array-key`           |                               |
| `number`              | same as `int\|float`          |
| `numeric`             | same as `int\|float\|numeric-string` |
| `scalar`              | same as `int\|float\|bool\|string` |
| `mixed`               |                               |

```php
$mapper->denormalize(42, 'int');         // 42
$mapper->denormalize('4.2', 'float');    // 4.2
$mapper->denormalize('42', 'numeric');   // '42'
$mapper->denormalize([1, 'a'], 'mixed'); // [1, 'a']
```

## Refined Types

Are checked on denormalization only.

| Type                                                | Accepts                                    |
|-----------------------------------------------------|--------------------------------------------|
| `positive-int`, `negative-int`                      | `> 0`, `< 0`                               |
| `non-positive-int`, `non-negative-int`              | `<= 0`, `>= 0`                             |
| `non-zero-int`                                      | `!= 0`                                     |
| `int<min, max>`                                     | a range, `min`/`max` for an open bound     |
| `positive-float`, `negative-float`                  | `> 0`, `< 0`                               |
| `non-positive-float`, `non-negative-float`          | `<= 0`, `>= 0`                             |
| `finite-float`                                      | not `INF`, `-INF`, `NAN`                   |
| `non-empty-string`                                  | not `''`                                   |
| `numeric-string`                                    | `'42'`, `'-1.5e3'`                         |
| `decimal-int-string`                                | an integer as PHP writes it: `'42'`, `'-5'`, not `'042'`, `'+5'` |
| `non-decimal-int-string`                            | any other string                           |
| `lowercase-string`                                  | no `A-Z`                                   |
| `uppercase-string`                                  | no `a-z`                                   |
| `non-empty-lowercase-string`                        | not `''`, no `A-Z`                         |
| `non-empty-uppercase-string`                        | not `''`, no `a-z`                         |
| `non-falsy-string`, `truthy-string`                 | not `''` and not `'0'`                     |
| `non-empty-literal-string`                          | not `''`                                   |
| `non-empty-scalar`, `empty-scalar`                  | a truthy scalar, a falsy scalar            |
| `non-empty-mixed`, `non-null-mixed`                 | a truthy value, not `null`                 |

```php
$mapper->denormalize(5, 'int<1, 10>'); // 5
$mapper->denormalize(42, 'int<1, 10>');
// throws InvalidValueException: $: Value must be of type "int<1, 10>"
// (between 1 and 10)

$mapper->denormalize('', 'non-empty-string');
// throws InvalidValueException: $: Value must be of type "non-empty-string"
// (non-empty)

$mapper->denormalize('abc', 'numeric-string');
// throws InvalidValueException: $: Value must be of type "numeric-string"
// (numeric)
```

<note>
<code>literal-string</code>, <code>callable-string</code>,
<code>class-string&lt;T&gt;</code>, <code>interface-string&lt;T&gt;</code>,
<code>trait-string</code> and <code>enum-string&lt;T&gt;</code> are accepted
as <code>string</code> without a check.
</note>

## Flags

`int-mask<...>` and `int-mask-of<...>`: an integer made of the given bits.

```php
final class Access
{
    public const READ = 1;
    public const WRITE = 2;
    public const EXECUTE = 4;
}

$mapper->denormalize(5, 'int-mask<1, 2, 4>');      // 5
$mapper->denormalize(8, 'int-mask<1, 2, 4>');
// throws InvalidValueException: $: Value must be of type "int-mask<1, 2, 4>"
// (bits of 7)

$mapper->denormalize(3, 'int-mask-of<Access::*>'); // 3
```

## Literals and Constants

```php
final class Http
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
}

$mapper->denormalize('active', '"active"|"blocked"'); // 'active'
$mapper->denormalize(null, 'null');                   // null
$mapper->denormalize('GET', 'Http::METHOD_GET');      // 'GET'
$mapper->denormalize('PUT', 'Http::METHOD_*');
// throws InvalidValueException: $: Value must be of type "Http::METHOD_*", but
// "PUT" given

$mapper->denormalize(\E_WARNING, 'E_*');              // 2
```

## Lists and Arrays

| Type                                       | Value                                     |
|--------------------------------------------|-------------------------------------------|
| `list<T>`, `non-empty-list<T>`             | an array with keys `0, 1, 2, ...`         |
| `array<K, V>`, `array<V>`, `V[]`           | any array, the key is `array-key` by default |
| `non-empty-array<K, V>`                    |                                           |
| `associative-array<K, V>`                  | same as `array<K, V>`                     |
| `iterable<K, V>`                           | an array or a `Traversable`, gives an array |

```php
$mapper->denormalize(['1', '2'], 'list<int>');                  // [1, 2]
$mapper->denormalize(['a' => '1'], 'array<string, int>');       // ['a' => 1]
$mapper->denormalize([], 'non-empty-list<int>');
// throws InvalidValueException: $: Value must be of type "non-empty-list<int>"
// (non-empty)

$mapper->normalize(new ArrayIterator([1, 2]), 'iterable<int>'); // [1, 2]
```

## Shapes

```php
$mapper->denormalize(['id' => '1', 'x' => 1], 'array{id: int, name?: string}');
// ['id' => 1]

$mapper->denormalize(['id' => '1', 'x' => 1], 'array{id: int, ...}');
// ['id' => 1, 'x' => 1]

$mapper->denormalize(['id' => 1], 'object{id: int}');
// (object) ['id' => 1]

$mapper->denormalize([], 'array{id: int}');
// throws InvalidValueException: $.id: Required field "id" is missing
```

- `name?:` — an optional field
- `...` — other fields are kept, without it they are dropped (or rejected by
  [Reject Extra Fields](mapper-configuration.md#reject-extra-fields))
- `object{...}` — gives `stdClass` on denormalization, takes the public
  properties of any object on normalization

## Unions and Nullable

A value of the exact type is taken first, then the types are tried with
[coercion](mapper-coercion.md).

```php
$mapper->denormalize('42', 'int|string'); // '42'
$mapper->denormalize(4.0, 'int|string');  // 4
$mapper->denormalize(null, '?int');       // null
```

## Enums

A backed enum is mapped by its value, a unit enum by the name of a case.

```php
$mapper->denormalize('active', Status::class);       // Status::Active
$mapper->denormalize('Hearts', Suit::class);         // Suit::Hearts
$mapper->normalize(Status::Active);                  // 'active'
$mapper->denormalize('blocked', 'value-of<Status>'); // 'blocked'
```

Other accepted values of a case: see
[value aliases](mapper-metadata-misc.md#value-aliases).

## Dates

`DateTimeInterface`, `DateTimeImmutable`, `DateTime` and their subclasses. The
format is an optional argument.

```php
$mapper->denormalize('2026-01-02', "DateTimeImmutable<'Y-m-d'>");
// DateTimeImmutable('2026-01-02T00:00:00+00:00')

$mapper->denormalize('02.01.2026', "DateTimeImmutable<'Y-m-d'>");
// throws InvalidValueException: $: Value must be of type
// "DateTimeImmutable<'Y-m-d'>", but "02.01.2026" given

$mapper->normalize(new DateTimeImmutable('2026-01-02 03:04:05+00:00'));
// '2026-01-02T03:04:05+00:00'

$mapper->denormalize(1767225600, "DateTimeImmutable<'U'>");
// DateTimeImmutable('2026-01-01T00:00:00+00:00')
```

<note>
Without a format a string is parsed by the constructor of the class, and
a date is normalized to RFC 3339. Use the <code>'U'</code> format for
timestamps.
</note>

## Objects

| Type         | Value                                                  |
|--------------|--------------------------------------------------------|
| a class      | see [metadata](mapper-metadata.md)                     |
| `stdClass`   | any fields                                             |
| `object`     | `stdClass` on denormalization, any object on normalization |

```php
$mapper->denormalize(['a' => 1], 'object');        // (object) ['a' => 1]
$mapper->normalize((object) ['a' => 1], 'object'); // ['a' => 1]
```
