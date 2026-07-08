# @psalm-readonly-allow-private-mutation

<primary-label ref="phpdoc-component"/>

The `@psalm-readonly-allow-private-mutation` tag allows a readonly
property to be mutated from within the declaring class, relaxing
the usual [@psalm-readonly](psalm-readonly-tag.md) constraint for
internal use only.

```
"@psalm-readonly-allow-private-mutation" [ <Description> ]
```

Parsing a `@psalm-readonly-allow-private-mutation` tag produces a `ReadonlyAllowPrivateMutationTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class ReadonlyAllowPrivateMutationTag extends FlagTag {}
```

The same `ReadonlyAllowPrivateMutationTag` is produced by [@phpstan-readonly-allow-private-mutation](phpstan-readonly-allow-private-mutation-tag.md), which restate the
same concept for their respective tools.

Defined by [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/).
