# @phpstan-assert-if-true

<primary-label ref="phpdoc-component"/>

The `@phpstan-assert-if-true` tag asserts the given type when the
function returns `true`, defined by PHPStan. It is one of the
[@phpstan-assert](phpstan-assert-tag.md) family of narrowing tags.

```
"@phpstan-assert-if-true" [ "!" | "=" | "!=" ] <Type> <Subject> [ <Description> ]
```

Parsing a `@phpstan-assert-if-true` tag produces an `AssertIfTrueTag` instance, carrying the
asserted `$type`, the `$subject` it is asserted of and the `$operator`
relating them, alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class AssertIfTrueTag extends AssertionTag {}
```

The same `AssertIfTrueTag` is produced by [@psalm-assert-if-true](psalm-assert-if-true-tag.md) and [@phan-assert-if-true](phan-assert-if-true-tag.md), which restate the same
assertion for their respective tools.

Documented by
[PHPStan](https://phpstan.org/writing-php-code/phpdocs-basics#narrowing-types-after-function-call).
