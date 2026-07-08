# @phpstan-pure

<primary-label ref="phpdoc-component"/>

The `@phpstan-pure` tag declares a function or method as pure — free
of side effects, always returning the same result for the same
arguments. It is defined by the static analyzer PHPStan.

```
"@phpstan-pure" [ <Description> ]
```

Parsing a `@phpstan-pure` tag produces a `PureTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PureTag extends FlagTag {}
```

The same `PureTag` is produced by [@psalm-pure](psalm-pure-tag.md) and [@phan-pure](phan-pure-tag.md), which restate the
same concept for their respective tools.

See PHPStan's
[PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics#impure-functions).
