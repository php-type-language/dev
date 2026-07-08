# @phan-assert-if-false

<primary-label ref="phpdoc-component"/>

The `@phan-assert-if-false` tag asserts the given type for an
argument or variable when the function returns `false`. It belongs
to the same assertion family as [@phan-assert](phan-assert-tag.md)
and its counterpart
[@phan-assert-if-true](phan-assert-if-true-tag.md).

```
"@phan-assert-if-false" <Type> <Variable> [ <Description> ]
```

Parsing a `@phan-assert-if-false` tag produces an `AssertIfFalseTag` instance, carrying the
asserted `$type` and `$variable` alongside the `$name` and optional
`$description` every [Tag](phpdoc.md#tag) already provides.

```php
final class AssertIfFalseTag extends TypedVariableTag {}
```

The same `AssertIfFalseTag` is produced by [@psalm-assert-if-false](psalm-assert-if-false-tag.md) and [@phpstan-assert-if-false](phpstan-assert-if-false-tag.md), which restate the same
assertion for their respective tools.

Defined by Phan; see the "Assertions" section of
[Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
