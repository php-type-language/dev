# @phpstan-assert

<primary-label ref="phpdoc-component"/>

The `@phpstan-assert` tag asserts that an argument or variable is
narrowed to a given type after the call, defined by PHPStan. It has
`-if-true` and `-if-false` siblings for assertions that only hold when a
boolean-returning function returns a particular value.

```
"@phpstan-assert" <Type> <Variable> [ <Description> ]
```

Parsing a `@phpstan-assert` tag produces an `AssertTag` instance, carrying the
asserted `$type` and `$variable` alongside the `$name` and optional
`$description` every [Tag](phpdoc.md#tag) already provides.

```php
final class AssertTag extends TypedVariableTag {}
```

The same `AssertTag` is produced by [@psalm-assert](psalm-assert-tag.md) and [@phan-assert](phan-assert-tag.md), which restate the same
assertion for their respective tools.

Documented by
[PHPStan](https://phpstan.org/writing-php-code/phpdocs-basics#narrowing-types-after-function-call).
