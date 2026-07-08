# @phan-real-return

<primary-label ref="phpdoc-component"/>

The `@phan-real-return` tag documents the actual native return
type of a function or method, kept distinct from the type
documented via [@return](return-tag.md).

```
"@phan-real-return" <Type> [ <Description> ]
```

Parsing a `@phan-real-return` tag produces a `PhanRealReturnTag` instance, carrying the
parsed `$type` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class PhanRealReturnTag extends TypedTag {}
```

Defined by Phan, a static analyzer for PHP; no dedicated
documentation page could be confirmed for this tag.
