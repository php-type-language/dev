# @psalm-allow-private-mutation

<primary-label ref="phpdoc-component"/>

The `@psalm-allow-private-mutation` tag allows a private-scope mutation
of an otherwise immutable property. It is defined by the static
analyzer Psalm.

```
"@psalm-allow-private-mutation" [ <Description> ]
```

Parsing a `@psalm-allow-private-mutation` tag produces a `AllowPrivateMutationTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class AllowPrivateMutationTag extends FlagTag {}
```

The same `AllowPrivateMutationTag` is produced by [@phpstan-allow-private-mutation](phpstan-allow-private-mutation-tag.md), which restate the
same concept for their respective tools.

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-allow-private-mutation).
