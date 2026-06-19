# Types

Types are runtime objects (not compiled or cached) that are responsible for 
checking a value for conformance and for converting from one value to another.

```php
interface TypeInterface
{
    // Check value
    public function match(mixed $value, Context $context): bool;

    // Cast value
    public function cast(mixed $value, Context $context): mixed;
}
```

- When calling the `Mapper::normalize()` or `Mapper::denormalize()` method, the 
  `TypeInterface::cast()`<sup>[1]</sup> method will be called.
  - *[1]: This isn't always the case. For example, union types additionally 
    call the `match()` method to determine the appropriate type.* 
- When calling the `Mapper::isNormalizable()` or `Mapper::isDenormalizable()` 
  method, the `TypeInterface::match()` method will be called.

## Definitions

The type cannot be explicitly registered in the mapper
([in the platform](mapper-platforms.md)). Instead of registering types, type builders 
are used that analyze the "type definition" and create a specific type based 
on it.

For example:

```php
// Calls the "cast()" method of the built-in IntType type
$mapper->normalize(..., 'int');

// Calls the "cast()" method of the built-in IntRangeType type
$mapper->normalize(..., 'int<0, max>');
```

As you can see, the signature (the "type definition") of the type is 
different. The mapper notices this and selects the appropriate type instance 
based on it.

<note>
For more information about type builders, see the
<a href="type-builders.md">"type builders" documentation page</a>.
</note>

## Directions

It is worth noting that when using the mapper there are two directions:
- Normalization 
- Denormalization

Thus, some types may have multiple implementations depending on which direction
the transformation occurs. For example, a built-in "backed enum" type:

```php
enum Some: string
{
    case Example = 'value';
}

// Enum normalization uses 
// object-to-string conversion
$mapper->normalize(Some::Example, 'Some'); // string("value")

// Enam denormalization uses 
// string-to-enum object conversion
$mapper->denormalize('value', 'Some');     // object(Some::Example)
```

A type can be responsible for one thing, for example, converting 
from an enum to a string. However, if desired, the direction can be determined 
at runtime within the type itself. However, this method is not recommended.

<note>
For more information on creating custom types for the mapper, 
see the <a href="custom-type.md">"custom type" documentation page</a>.
</note>