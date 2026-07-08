# @psalm-taint-escape

<primary-label ref="phpdoc-component"/>

The `@psalm-taint-escape` tag marks a value as no longer tainted
after passing through the described element. It is part of Psalm's
taint-analysis annotations.

```
"@psalm-taint-escape" <Name> [ <Description> ]
```

Parsing a `@psalm-taint-escape` tag produces a `PsalmTaintEscapeTag` instance, carrying the
parsed `$identifier` alongside the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already provides.

```php
final class PsalmTaintEscapeTag extends IdentifierTag {}
```

See [Psalm's security analysis annotations](https://psalm.dev/docs/security_analysis/annotations/).
