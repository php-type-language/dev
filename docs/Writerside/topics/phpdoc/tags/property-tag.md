# @property

<primary-label ref="phpdoc-component"/>

The `@property` tag declares a "magic" property: one that is readable and
writable through `__get`/`__set` (or `ArrayAccess`-style tricks) but never
appears as an actual declared property in the class body. Because such
properties are invisible to reflection and to PHP's own type system, IDEs
and static analyzers have no way to know they exist — or what type they
hold — unless the class itself documents them. `@property` is written on
the class docblock, not on any property or method, typically with one line
per magic property the class exposes.

```
"@property" <Type> <Variable> [ <Description> ]
```

> A single magic property on a value object.
> ```php
> /**
>  * @property string $name
>  */
> class User
> {
>     public function __get(string $name): mixed
>     {
>         /* ... */
>     }
>
>     public function __set(string $name, mixed $value): void {
>         /* ... */
>     }
> }
> ```

> Several magic properties, with descriptions.
> ```php
> /**
>  * @property int $id The record's primary key.
>  * @property DateTimeImmutable $createdAt When the record was
>  *           inserted.
>  */
> class Record {}
> ```

Parsing a `@property` tag produces a `TypedVariableTag` instance exposing:

* `$type` — the documented `TypeNode`.
* `$variable` — the property's name, without the leading `$`.

```php
final class PropertyTag extends MagicPropertyTag
{
    // Inherits `$type` (computed `TypeNode` getter)
    // and `string $variable` from MagicPropertyTag
    // (itself a TypedVariableTag), plus `$name` and
    // `$description` from the base `Tag` class; adds
    // nothing of its own.
}
```

This is the same shape as [@param](param-tag.md): a type paired with a
name. When a magic property only supports one direction, the more specific
[@property-read](property-read-tag.md) or
[@property-write](property-write-tag.md) tag should be used instead, so
consumers know not to attempt the unsupported operation.

Defined by [PSR-19 §5.11](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#511-property)
— which groups `@property`, `@property-read`, and
`@property-write` under a single section — and
phpDocumentor's own [`@property` reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/property.html).
