# @phpstan-import-type

<primary-label ref="phpdoc-component"/>

The `@phpstan-import-type` tag imports a `@phpstan-type` alias declared
in another class, defined by PHPStan.

```
"@phpstan-import-type" <Name> "from" <Type>
    [ "as" <Name> ]
```

Parsing a `@phpstan-import-type` tag produces an `ImportTypeAliasTag` instance,
carrying the imported `$alias`, the `$type` it is imported from, and the
optional local `$as` name alongside the `$name` every [Tag](phpdoc.md#tag)
already provides.

```php
final class ImportTypeAliasTag extends Tag {}
```

The same `ImportTypeAliasTag` is produced by [@psalm-import-type](psalm-import-type-tag.md), which restates
the same concept for Psalm.

Documented by
[PHPStan](https://phpstan.org/writing-php-code/phpdoc-types#local-type-aliases).
