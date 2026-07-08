# @phpstan-readonly-allow-private-mutation

<primary-label ref="phpdoc-component"/>

The `@phpstan-readonly-allow-private-mutation` tag allows a readonly
property to be mutated from within the declaring class, rather than
only during initialization. It is defined by the static analyzer
PHPStan.

```
"@phpstan-readonly-allow-private-mutation" [ <Description> ]
```

Parsing a `@phpstan-readonly-allow-private-mutation` tag produces a `ReadonlyAllowPrivateMutationTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class ReadonlyAllowPrivateMutationTag extends FlagTag {}
```

The same `ReadonlyAllowPrivateMutationTag` is produced by [@psalm-readonly-allow-private-mutation](psalm-readonly-allow-private-mutation-tag.md), which restate the
same concept for their respective tools.
