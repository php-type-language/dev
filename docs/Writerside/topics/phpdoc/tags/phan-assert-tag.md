# @phan-assert

<primary-label ref="phpdoc-component"/>

The `@phan-assert` tag asserts that an argument or variable is
narrowed to a given type after the call returns. Phan documents it
together with its `@phan-assert-if-true` and `@phan-assert-if-false`
siblings — see [@phan-assert-if-true](phan-assert-if-true-tag.md) and
[@phan-assert-if-false](phan-assert-if-false-tag.md) — as part of the
same assertion family.

```
"@phan-assert" <Type> <Variable> [ <Description> ]
```

Parsing a `@phan-assert` tag produces an `AssertTag` instance, carrying the
asserted `$type` and `$variable` alongside the `$name` and optional
`$description` every [Tag](phpdoc.md#tag) already provides.

```php
final class AssertTag extends TypedVariableTag {}
```

The same `AssertTag` is produced by [@psalm-assert](psalm-assert-tag.md) and [@phpstan-assert](phpstan-assert-tag.md), which restate the same
assertion for their respective tools.

Defined by Phan; see the "Assertions" section of
[Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
