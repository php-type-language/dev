# @phan-side-effect-free

<primary-label ref="phpdoc-component"/>

The `@phan-side-effect-free` tag declares a function or method
as free of side effects.

```
"@phan-side-effect-free" [ <Description> ]
```

Parsing a `@phan-side-effect-free` tag produces a `PhanSideEffectFreeTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PhanSideEffectFreeTag extends FlagTag {}
```

Defined by [Phan](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#phan-side-effect-free).
