# @psalm-import-type

<primary-label ref="phpdoc-component"/>

The `@psalm-import-type` tag imports a `@psalm-type` alias declared in
another class, optionally binding it to a local alias name.

```
"@psalm-import-type" <Name> "from" <Type>
    [ "as" <Name> ]
```

Parsing a `@psalm-import-type` tag produces an `ImportTypeAliasTag` instance,
carrying the imported `$alias`, the `$type` it is imported from, and the
optional local `$as` name alongside the `$name` every [Tag](phpdoc.md#tag)
already provides.

```php
final class ImportTypeAliasTag extends Tag {}
```

The same `ImportTypeAliasTag` is produced by [@phpstan-import-type](phpstan-import-type-tag.md), which restates
the same concept for PHPStan.

Defined by [Psalm's supported annotations reference](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-import-type).
