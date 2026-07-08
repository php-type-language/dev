# @psalm-stub-override

<primary-label ref="phpdoc-component"/>

The `@psalm-stub-override` tag marks a stub declaration as
intentionally overriding the real signature, telling Psalm not to
flag the mismatch between the two.

```
"@psalm-stub-override" [ <Description> ]
```

Parsing a `@psalm-stub-override` tag produces a `PsalmStubOverrideTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmStubOverrideTag extends FlagTag {}
```

Defined by Psalm.
