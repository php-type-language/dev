# @phpstan-assert-if-false

<primary-label ref="phpdoc-component"/>

The `@phpstan-assert-if-false` tag asserts the given type when the
function returns `false`, defined by PHPStan. It is one of the
[@phpstan-assert](phpstan-assert-tag.md) family of narrowing tags.

```
"@phpstan-assert-if-false" <Type> <Variable> [ <Description> ]
```

Parsing a `@phpstan-assert-if-false` tag produces an `AssertIfFalseTag` instance, carrying the
asserted `$type` and `$variable` alongside the `$name` and optional
`$description` every [Tag](phpdoc.md#tag) already provides.

```php
final class AssertIfFalseTag extends TypedVariableTag {}
```

The same `AssertIfFalseTag` is produced by [@psalm-assert-if-false](psalm-assert-if-false-tag.md) and [@phan-assert-if-false](phan-assert-if-false-tag.md), which restate the same
assertion for their respective tools.

Documented by
[PHPStan](https://phpstan.org/writing-php-code/phpdocs-basics#narrowing-types-after-function-call).
