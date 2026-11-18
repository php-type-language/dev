# Type Coercion

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

On denormalization a value written in another form is converted to the type,
when it is the same value: `'42'` to `int` is `42`, `'4.2'` is not.
Normalization never converts.

<warning>
Coercion is disabled by the <a href="mapper-configuration.md#strict">strict
types</a> option and by the
<a href="mapper-metadata-types.md#strict-types">strict</a> metadata of a
property, except an <code>int</code> as a <code>float</code>, a
<code>stdClass</code> as an array and the keys of arrays.
</warning>

## int

```php
$mapper->denormalize('42', 'int');   // 42
$mapper->denormalize('-0', 'int');   // 0
$mapper->denormalize('42.0', 'int'); // 42
$mapper->denormalize(42.0, 'int');   // 42
$mapper->denormalize(true, 'int');
// throws InvalidValueException: $: Value must be of type int, but true given

$mapper->denormalize('4.2', 'int');
// throws InvalidValueException: $: Value must be of type int, but "4.2" given

$mapper->denormalize(' 42', 'int');
// throws InvalidValueException: $: Value must be of type int, but " 42" given

$mapper->denormalize('1e3', 'int');
// throws InvalidValueException: $: Value must be of type int, but "1e3" given
```

## float

```php
$mapper->denormalize(42, 'float');    // 42.0
$mapper->denormalize('4.2', 'float'); // 4.2
$mapper->denormalize('1e3', 'float'); // 1000.0
$mapper->denormalize('.5', 'float');  // 0.5
$mapper->denormalize('1e400', 'float');
// throws InvalidValueException: $: Value must be of type float, but "1e400"
// given

$mapper->denormalize(true, 'float');
// throws InvalidValueException: $: Value must be of type float, but true
// given
```

<note>
An integer without an exact <code>float</code> value, such as
<code>2 ** 53 + 1</code>, is rejected.
</note>

## string

```php
$mapper->denormalize(42, 'string');  // '42'
$mapper->denormalize(4.2, 'string'); // '4.2'
$mapper->denormalize(true, 'string');
// throws InvalidValueException: $: Value must be of type string, but true
// given

$mapper->denormalize(null, 'string');
// throws InvalidValueException: $: Value must be of type string, but null
// given
```

## bool

```php
$mapper->denormalize(1, 'bool');      // true
$mapper->denormalize('0', 'bool');    // false
$mapper->denormalize('true', 'bool'); // true
$mapper->denormalize('yes', 'bool');  // true
$mapper->denormalize('off', 'bool');  // false
$mapper->denormalize(2, 'bool');
// throws InvalidValueException: $: Value must be of type bool, but 2 given
```

## array

A `stdClass` (such as from `json_decode()`) is accepted as the data of a
list, an array, a shape or a class, also with strict types. Other objects
are not.

```php
$mapper->denormalize((object) ['a' => '1'], 'array<string, int>'); // ['a' => 1]
```

<note>
Keys of arrays are always converted, even with strict types: the keys of
PHP arrays are only <code>int</code> and <code>string</code>.
</note>

## Custom Coercers

Replace a coercer of the mapper by the name of its type: `int`, `float`,
`string`, `bool` or `array`.

```php
use TypeLang\Mapper\Coercer\CoercerInterface;
use TypeLang\Mapper\Coercer\FloatCoercer;
use TypeLang\Mapper\Mapper;

/**
 * @template-implements CoercerInterface<float>
 */
final class DecimalCommaCoercer implements CoercerInterface
{
    public function __construct(
        private readonly FloatCoercer $float = new FloatCoercer(),
    ) {}

    public function coerce(mixed $value): ?float
    {
        if (\is_string($value)) {
            $value = \str_replace(',', '.', $value);
        }

        return $this->float->coerce($value);
    }
}

$mapper = new Mapper(coercers: ['float' => new DecimalCommaCoercer()]);

$mapper->denormalize('4,2', 'float'); // 4.2
```

A coercer returns the converted value or `null` when the value cannot be
converted.
