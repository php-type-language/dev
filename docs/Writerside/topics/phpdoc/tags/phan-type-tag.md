# @phan-type

<primary-label ref="phpdoc-component"/>

The `@phan-type` tag declares a local alias for a complex type.

```
"@phan-type" <Name> [ "=" ] <Type>
```

Parsing a `@phan-type` tag produces a `TypeAliasTag` instance, carrying the
parsed `$alias` name and its `$type` alongside the `$name` every
[Tag](phpdoc.md#tag) already provides.

```php
final class TypeAliasTag extends Tag {}
```

The same `TypeAliasTag` is produced by [@psalm-type](psalm-type-tag.md) and [@phpstan-type](phpstan-type-tag.md), which restate
the same concept for their respective tools.

Defined by Phan, a static analyzer for PHP; no dedicated
documentation page could be confirmed for this tag.
