# @phpstan-self-out

<primary-label ref="phpdoc-component"/>

The `@phpstan-self-out` tag documents the refined type of `$this`
after a method call, letting PHPStan track how a fluent or
mutating method narrows the object's own type. It is defined by the
static analyzer PHPStan, which also accepts the alias
`@phpstan-this-out` (see [phpstan-this-out-tag.md](phpstan-this-out-tag.md)).

```
"@phpstan-self-out" <Type> [ <Description> ]
```

Parsing a `@phpstan-self-out` tag produces a `SelfOutTag` instance, carrying the
parsed `$type` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class SelfOutTag extends TypedTag {}
```

The same `SelfOutTag` is produced by [@psalm-self-out](psalm-self-out-tag.md), which restates the same concept for its tool.

See PHPStan's
[PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics#change-type-of-current-object-after-calling-a-method).
