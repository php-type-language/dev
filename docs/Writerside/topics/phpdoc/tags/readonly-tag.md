# @readonly

<primary-label ref="phpdoc-component"/>

The `@readonly` tag declares that a property may only be written once,
during initialization, and must not change afterwards. It is the
documentation-level equivalent of PHP's native `readonly` property modifier,
used where the native modifier cannot be applied — most commonly on magic
or virtual properties exposed through `__get`/`__set` and documented with
[@property](property-tag.md) rather than declared as real class members.

```
"@readonly" [ <Description> ]
```

> A magic property that is only ever set once, from the constructor.
> ```php
> /**
>  * @property-read string $id
>  * @readonly
>  */
> class Entity
> {
> }
> ```

> With a description explaining why it isn't a native readonly property.
> ```php
> /**
>  * @readonly Assigned lazily on first access; kept
>  *     mutable internally.
>  */
> public $identifier;
> ```

Parsing a `@readonly` tag produces a `ReadonlyTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class ReadonlyTag extends FlagTag {}
```

Unlike most tags in this reference, `@readonly` is not part of the
PSR-19 draft or phpDocumentor's own tag list. Psalm and PHPStan each
ship their own independent support for it — distinct from PHP 8.1's
native `readonly` property modifier that inspired its name. See
[Psalm's `@readonly` documentation](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-readonly-and-readonly)
and [PHPStan's PHPDoc basics guide](https://phpstan.org/writing-php-code/phpdocs-basics).
