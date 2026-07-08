# @psalm-taint-source

<primary-label ref="phpdoc-component"/>

The `@psalm-taint-source` tag marks the return value as a taint
source of the given type. It is part of Psalm's taint-analysis
annotations.

```
"@psalm-taint-source" <Name> [ <Description> ]
```

Parsing a `@psalm-taint-source` tag produces a `PsalmTaintSourceTag` instance, carrying the
parsed `$identifier` alongside the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already provides.

```php
final class PsalmTaintSourceTag extends IdentifierTag {}
```

See [Psalm's security analysis annotations](https://psalm.dev/docs/security_analysis/annotations/#psalm-taint-source-lttaint-typegt).
