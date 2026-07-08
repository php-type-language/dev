# @psalm-assert-if-true

<primary-label ref="phpdoc-component"/>

The `@psalm-assert-if-true` tag asserts the given type for an
argument or variable when the function returns `true`. It is
defined by the static analyzer Psalm, alongside
[@psalm-assert](psalm-assert-tag.md).

```
"@psalm-assert-if-true" <Type> <Variable> [ <Description> ]
```

Parsing a `@psalm-assert-if-true` tag produces an `AssertIfTrueTag` instance, carrying the
asserted `$type` and `$variable` alongside the `$name` and optional
`$description` every [Tag](phpdoc.md#tag) already provides.

```php
final class AssertIfTrueTag extends TypedVariableTag {}
```

The same `AssertIfTrueTag` is produced by [@phpstan-assert-if-true](phpstan-assert-if-true-tag.md) and [@phan-assert-if-true](phan-assert-if-true-tag.md), which restate the same
assertion for their respective tools.

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-assert-psalm-assert-if-true-psalm-assert-if-false-psalm-if-this-is-and-psalm-this-out).
