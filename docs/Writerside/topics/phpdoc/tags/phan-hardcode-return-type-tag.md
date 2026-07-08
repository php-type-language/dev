# @phan-hardcode-return-type

<primary-label ref="phpdoc-component"/>

The `@phan-hardcode-return-type` tag forces Phan to use the
documented return type instead of the type it would otherwise infer
from the method body.

```
"@phan-hardcode-return-type" <Type> [ <Description> ]
```

Parsing a `@phan-hardcode-return-type` tag produces a `PhanHardcodeReturnTypeTag` instance, carrying the
parsed `$type` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class PhanHardcodeReturnTypeTag extends TypedTag {}
```

Defined by Phan.
