# @psalm-taint-unescape

<primary-label ref="phpdoc-component"/>

The `@psalm-taint-unescape` tag marks a value as tainted again after
passing through the described element, reversing an earlier
[@psalm-taint-escape](psalm-taint-escape-tag.md). It is part of
Psalm's taint-analysis annotations.

```
"@psalm-taint-unescape" <Name> [ <Description> ]
```

Parsing a `@psalm-taint-unescape` tag produces a `PsalmTaintUnescapeTag` instance, carrying the
parsed `$identifier` alongside the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already provides.

```php
final class PsalmTaintUnescapeTag extends IdentifierTag {}
```

See [Psalm's security analysis annotations](https://psalm.dev/docs/security_analysis/annotations/).
