# @psalm-yield

<primary-label ref="phpdoc-component"/>

The `@psalm-yield` tag documents the type yielded by a `Generator`.
It is defined by the static analyzer Psalm.

```
"@psalm-yield" <Type> [ <Description> ]
```

Parsing a `@psalm-yield` tag produces a `YieldTag` instance, carrying the
parsed `$type` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class YieldTag extends TypedTag {}
```

The same `YieldTag` is produced by [@phpstan-yield](phpstan-yield-tag.md), which restates the same concept for its tool.

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-yield).
