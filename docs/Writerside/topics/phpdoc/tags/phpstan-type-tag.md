# @phpstan-type

<primary-label ref="phpdoc-component"/>

The `@phpstan-type` tag declares a local alias for a complex type,
so it can be referenced by name in later `@param`, `@return`, and
`@var` tags. It is defined by the static analyzer PHPStan.

```
"@phpstan-type" <Name> [ "=" ] <Type>
```

Parsing a `@phpstan-type` tag produces a `TypeAliasTag` instance, carrying the
parsed `$alias` name and its `$type` alongside the `$name` every
[Tag](phpdoc.md#tag) already provides.

```php
final class TypeAliasTag extends Tag {}
```

The same `TypeAliasTag` is produced by [@psalm-type](psalm-type-tag.md) and [@phan-type](phan-type-tag.md), which restate
the same concept for their respective tools.

See PHPStan's
[PHPDoc types](https://phpstan.org/writing-php-code/phpdoc-types#local-type-aliases).
