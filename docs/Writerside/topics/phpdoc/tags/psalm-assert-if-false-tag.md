# @psalm-assert-if-false

<primary-label ref="phpdoc-component"/>

The `@psalm-assert-if-false` tag asserts the given type for an
argument or variable when the function returns `false`. It is
defined by the static analyzer Psalm, alongside
[@psalm-assert](psalm-assert-tag.md).

```
"@psalm-assert-if-false" <Type> <Variable> [ <Description> ]
```

Parsing a `@psalm-assert-if-false` tag produces an `AssertIfFalseTag` instance, carrying the
asserted `$type` and `$variable` alongside the `$name` and optional
`$description` every [Tag](phpdoc.md#tag) already provides.

```php
final class AssertIfFalseTag extends TypedVariableTag {}
```

The same `AssertIfFalseTag` is produced by [@phpstan-assert-if-false](phpstan-assert-if-false-tag.md) and [@phan-assert-if-false](phan-assert-if-false-tag.md), which restate the same
assertion for their respective tools.

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-assert-psalm-assert-if-true-psalm-assert-if-false-psalm-if-this-is-and-psalm-this-out).
