# @psalm-type

<primary-label ref="phpdoc-component"/>

The `@psalm-type` tag declares a local alias for a complex type,
which can then be reused across the codebase. It is defined by the
static analyzer Psalm.

```
"@psalm-type" <Name> [ "=" ] <Type>
```

Parsing a `@psalm-type` tag produces a `TypeAliasTag` instance, carrying the
parsed `$alias` name and its `$type` alongside the `$name` every
[Tag](phpdoc.md#tag) already provides.

```php
final class TypeAliasTag extends Tag {}
```

The same `TypeAliasTag` is produced by [@phpstan-type](phpstan-type-tag.md) and [@phan-type](phan-type-tag.md), which restate
the same concept for their respective tools.

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-type).
