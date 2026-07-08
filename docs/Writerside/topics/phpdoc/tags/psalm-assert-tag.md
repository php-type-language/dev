# @psalm-assert

<primary-label ref="phpdoc-component"/>

The `@psalm-assert` tag asserts that an argument or variable is
narrowed to a given type after the call returns. It is defined by
the static analyzer Psalm.

```
"@psalm-assert" <Type> <Variable> [ <Description> ]
```

Parsing a `@psalm-assert` tag produces an `AssertTag` instance, carrying the
asserted `$type` and `$variable` alongside the `$name` and optional
`$description` every [Tag](phpdoc.md#tag) already provides.

```php
final class AssertTag extends TypedVariableTag {}
```

The same `AssertTag` is produced by [@phpstan-assert](phpstan-assert-tag.md) and [@phan-assert](phan-assert-tag.md), which restate the same
assertion for their respective tools.

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-assert-psalm-assert-if-true-psalm-assert-if-false-psalm-if-this-is-and-psalm-this-out).
