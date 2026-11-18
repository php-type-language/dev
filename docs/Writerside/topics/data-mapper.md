# Type Mapper

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

A mapper converts data by a [type](introduction.md) in both directions:

- **Denormalization** — external data (decoded JSON, a form, a database row)
  into PHP values: objects, enums, dates, typed lists.
- **Normalization** — PHP values back into plain data (scalars, arrays) ready
  for `json_encode()`.

## Installation

<tldr>
    <p>
        Via <a href="https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies">Composer</a>:
        <code lang="bash">composer require type-lang/mapper</code>
    </p>
</tldr>

**Requirements:**
* `PHP >= 8.1`

## Usage

```php
use TypeLang\Mapper\Mapper;

final class Address
{
    public string $city;
}

final class User
{
    public int $id;
    public string $name;
    public Address $address;
}

$mapper = new Mapper();

$user = $mapper->denormalize([
    'id' => 42,
    'name' => 'Alice',
    'address' => ['city' => 'Paris'],
], User::class);

$mapper->normalize($user);
// ['id' => 42, 'name' => 'Alice', 'address' => ['city' => 'Paris']]
```

The type is not only a class, but any of the
[supported types](mapper-types.md):

```php
$mapper->denormalize('42', 'int');
// 42

$mapper->denormalize([['id' => 1, 'name' => 'Alice']], 'list<User>');
// [User { id: 1, name: 'Alice' }]

$mapper->denormalize(['page' => '2'], 'array{page: positive-int, size?: int}');
// ['page' => 2]

$mapper->denormalize('auto', 'int|"auto"');
// 'auto'
```

<note>
The type of <code>normalize()</code> is optional: without it a value is
normalized by its own type (an object by its class, an array element by
element).
</note>

## Validation

`isDenormalizable()` and `isNormalizable()` return whether a value can be
converted, without throwing on invalid data:

```php
final class User
{
    public int $id;
}

$mapper->isDenormalizable(['id' => 42], User::class);    // true
$mapper->isDenormalizable(['id' => 'abc'], User::class); // false
```

Invalid data throws an exception with the path of the wrong value:

```php
$mapper->denormalize(['id' => 'abc'], User::class);
// throws InvalidValueException: $.id: Value must be of type int, but "abc"
// given
```

See [errors](mapper-errors.md) for details.

## What's Next

- [Configuration](mapper-configuration.md) — options of the mapper
- [Metadata](mapper-metadata.md) — how classes are mapped and where it is
  described
- [Types](mapper-types.md) — all supported types
- [Type Coercion](mapper-coercion.md) — which values are converted
- [Errors](mapper-errors.md) — exceptions and messages
- [Extending](mapper-extending.md) — custom types, hooks, strategies
- [PHPStan](mapper-phpstan.md) — static analysis of type strings
