# @property-read

<primary-label ref="phpdoc-component"/>

The `@property-read` tag declares a magic property that can only be read,
never written: `__get` is expected to handle it, but `__set` should
reject it (or simply doesn't exist). It is otherwise identical to
[@property](property-tag.md) — written on the class docblock, one line per
property — and exists so that IDEs and static analyzers can flag an
accidental assignment to a value that the class was never meant to accept.

```
"@property-read" <Type> <Variable> [ <Description> ]
```

> A computed value with no corresponding setter.
> ```php
> /**
>  * @property-read int $length
>  */
> class ImmutableString
> {
>     public function __get(string $name): mixed { /* ... */ }
> }
> ```

> A read-only property with a description.
> ```php
> /**
>  * @property-read DateTimeImmutable $createdAt When the record was
>  *     inserted.
>  */
> class Record {}
> ```

Parsing a `@property-read` tag produces a `TypedVariableTag` instance
exposing:

* `$type` — the documented `TypeNode`.
* `$variable` — the property's name, without the leading `$`.

```php
final class PropertyReadTag extends MagicPropertyTag
{
    // Inherits `$type` (computed `TypeNode` getter)
    // and `string $variable` (required, non-null)
    // from MagicPropertyTag; adds nothing of its own.
}
```

Use [@property](property-tag.md) instead when both directions are
supported, or [@property-write](property-write-tag.md) for the mirror
case of a write-only property.

Defined by the same [PSR-19 §5.11](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#511-property)
section as `@property`, and phpDocumentor's own
[`@property-read` reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/property-read.html).
