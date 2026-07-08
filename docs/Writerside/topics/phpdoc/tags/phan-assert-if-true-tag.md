# @phan-assert-if-true

<primary-label ref="phpdoc-component"/>

The `@phan-assert-if-true` tag asserts the given type for an
argument or variable when the function returns `true`. It belongs
to the same assertion family as [@phan-assert](phan-assert-tag.md)
and its counterpart
[@phan-assert-if-false](phan-assert-if-false-tag.md).

```
"@phan-assert-if-true" <Type> <Variable> [ <Description> ]
```

Parsing a `@phan-assert-if-true` tag produces an `AssertIfTrueTag` instance, carrying the
asserted `$type` and `$variable` alongside the `$name` and optional
`$description` every [Tag](phpdoc.md#tag) already provides.

```php
final class AssertIfTrueTag extends TypedVariableTag {}
```

The same `AssertIfTrueTag` is produced by [@psalm-assert-if-true](psalm-assert-if-true-tag.md) and [@phpstan-assert-if-true](phpstan-assert-if-true-tag.md), which restate the same
assertion for their respective tools.

Defined by Phan; see the "Assertions" section of
[Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
