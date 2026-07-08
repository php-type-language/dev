# @phpstan-yield

<primary-label ref="phpdoc-component"/>

The `@phpstan-yield` tag documents the type yielded by a
`Generator`, distinct from the type it returns on completion. It is
defined by the static analyzer PHPStan.

```
"@phpstan-yield" <Type> [ <Description> ]
```

Parsing a `@phpstan-yield` tag produces a `YieldTag` instance, carrying the
parsed `$type` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class YieldTag extends TypedTag {}
```

The same `YieldTag` is produced by [@psalm-yield](psalm-yield-tag.md), which restates the same concept for its tool.

See PHPStan's [PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics) page.
