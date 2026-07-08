# @phpstan-allow-private-mutation

<primary-label ref="phpdoc-component"/>

The `@phpstan-allow-private-mutation` tag allows a private-scope mutation
of an otherwise immutable property, defined by PHPStan.

```
"@phpstan-allow-private-mutation" [ <Description> ]
```

Parsing a `@phpstan-allow-private-mutation` tag produces a `AllowPrivateMutationTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class AllowPrivateMutationTag extends FlagTag {}
```

The same `AllowPrivateMutationTag` is produced by [@psalm-allow-private-mutation](psalm-allow-private-mutation-tag.md), which restate the
same concept for their respective tools.

Originates from PHPStan's own annotation vocabulary.
