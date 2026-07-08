# @phan-write-only

<primary-label ref="phpdoc-component"/>

The `@phan-write-only` tag, paired with
[@phan-read-only](phan-read-only-tag.md), declares that a
property may only ever be written, never read.

```
"@phan-write-only" [ <Description> ]
```

Parsing a `@phan-write-only` tag produces a `PhanWriteOnlyTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PhanWriteOnlyTag extends FlagTag {}
```

Defined by [Phan](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#phan-read-only-and-phan-write-only).
