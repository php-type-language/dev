# @psalm-taint-sink

<primary-label ref="phpdoc-component"/>

The `@psalm-taint-sink` tag marks an argument as a taint sink for
the given taint type. It is part of Psalm's taint-analysis
annotations.

```
"@psalm-taint-sink" <Name> <Variable>
```

Parsing a `@psalm-taint-sink` tag produces a `PsalmTaintSinkTag` instance,
carrying the parsed `$taint` type and `$variable` alongside the `$name`
every [Tag](phpdoc.md#tag) already provides.

```php
final class PsalmTaintSinkTag extends Tag {}
```

See [Psalm's security analysis annotations](https://psalm.dev/docs/security_analysis/annotations/#psalm-taint-sink-lttaint-typegt-ltparam-namegt).
