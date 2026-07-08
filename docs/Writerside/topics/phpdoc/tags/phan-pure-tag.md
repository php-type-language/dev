# @phan-pure

<primary-label ref="phpdoc-component"/>

The `@phan-pure` tag declares a function or method as pure —
free of side effects, with a return value that depends only on
its arguments.

```
"@phan-pure" [ <Description> ]
```

Parsing a `@phan-pure` tag produces a `PureTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PureTag extends FlagTag {}
```

The same `PureTag` is produced by [@psalm-pure](psalm-pure-tag.md) and [@phpstan-pure](phpstan-pure-tag.md), which restate the
same concept for their respective tools.

Defined by Phan, a static analyzer for PHP; no dedicated
documentation page could be confirmed for this tag.
