# @psalm-mutation-free

<primary-label ref="phpdoc-component"/>

The `@psalm-mutation-free` tag declares that a method never mutates
any state, whether observable from outside the object or purely
internal to it.

```
"@psalm-mutation-free" [ <Description> ]
```

Parsing a `@psalm-mutation-free` tag produces a `PsalmMutationFreeTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmMutationFreeTag extends FlagTag {}
```

Defined by [Psalm's supported annotations reference](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-mutation-free).
