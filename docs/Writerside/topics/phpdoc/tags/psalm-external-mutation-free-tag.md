# @psalm-external-mutation-free

<primary-label ref="phpdoc-component"/>

The `@psalm-external-mutation-free` tag declares that a method never
mutates state observable from outside the object. It is defined by
the static analyzer Psalm.

```
"@psalm-external-mutation-free" [ <Description> ]
```

Parsing a `@psalm-external-mutation-free` tag produces a `PsalmExternalMutationFreeTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmExternalMutationFreeTag extends FlagTag {}
```

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-external-mutation-free).
