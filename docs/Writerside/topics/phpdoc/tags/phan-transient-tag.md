# @phan-transient

<primary-label ref="phpdoc-component"/>

The `@phan-transient` tag marks a property as excluded from
serialization.

```
"@phan-transient" [ <Description> ]
```

Parsing a `@phan-transient` tag produces a `PhanTransientTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PhanTransientTag extends FlagTag {}
```

Defined by Phan, a static analyzer for PHP; no dedicated
documentation page could be confirmed for this tag.
