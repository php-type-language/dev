# @property-write

<primary-label ref="phpdoc-component"/>

The `@property-write` tag declares a magic property that can only be
written, never read back: `__set` is expected to handle it, but `__get`
should reject it (or simply doesn't exist). It is the mirror image of
[@property-read](property-read-tag.md) — useful for write-only concerns
such as a setter that stores a hashed or otherwise transformed value that
the object never exposes in its original form.

```
"@property-write" <Type> <Variable> [ <Description> ]
```

> A setter with no corresponding getter.
> ```php
> /**
>  * @property-write string $password
>  */
> class Account
> {
>     public function __set(string $name, mixed $value): void {
>         /* ... */
>     }
> }
> ```

> A write-only property with a description.
> ```php
> /**
>  * @property-write LoggerInterface $logger Replaces the logger
>  *     used internally.
>  */
> class Service {}
> ```

Parsing a `@property-write` tag produces a `TypedVariableTag` instance
exposing:

* `$type` — the documented `TypeNode`.
* `$variable` — the property's name, without the leading `$`.

```php
final class PropertyWriteTag extends MagicPropertyTag
{
    // Same shape as PropertyReadTag: inherits `$type`
    // and `string $variable` from MagicPropertyTag;
    // adds nothing of its own.
}
```

Use [@property](property-tag.md) instead when both directions are
supported, or [@property-read](property-read-tag.md) for the mirror case
of a read-only property.

Defined by the same [PSR-19 §5.11](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#511-property)
section as `@property`, and phpDocumentor's own
[`@property-write` reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/property-write.html).
