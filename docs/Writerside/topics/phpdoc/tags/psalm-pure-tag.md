# @psalm-pure

<primary-label ref="phpdoc-component"/>

The `@psalm-pure` tag declares a function or method as pure, meaning
it is free of side effects: calling it repeatedly with the same
arguments always produces the same result, without observably
mutating any state.

```
"@psalm-pure" [ <Description> ]
```

Parsing a `@psalm-pure` tag produces a `PureTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PureTag extends FlagTag {}
```

The same `PureTag` is produced by [@phpstan-pure](phpstan-pure-tag.md) and [@phan-pure](phan-pure-tag.md), which restate the
same concept for their respective tools.

Defined by [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-pure).
