# @psalm-taint-specialize

<primary-label ref="phpdoc-component"/>

The `@psalm-taint-specialize` tag tells Psalm to track tainted data
separately per call site, rather than merging taint information from
all callers together. It is part of Psalm's taint-analysis
annotations.

```
"@psalm-taint-specialize" [ <Description> ]
```

Parsing a `@psalm-taint-specialize` tag produces a `PsalmTaintSpecializeTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmTaintSpecializeTag extends FlagTag {}
```

See [Psalm's security analysis annotations](https://psalm.dev/docs/security_analysis/annotations/).
