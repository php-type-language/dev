# @psalm-check-type

<primary-label ref="phpdoc-component"/>

The `@psalm-check-type` tag asserts, for debugging purposes, that a
variable resolves to a given type. It is defined by the static
analyzer Psalm.

```
"@psalm-check-type" <Variable> "=" <Type>
```

Parsing a `@psalm-check-type` tag produces a `PsalmCheckTypeTag` instance,
carrying the checked `$variable` and its expected `$type` alongside the
`$name` every [Tag](phpdoc.md#tag) already provides.

```php
final class PsalmCheckTypeTag extends CheckTypeTag {}
```

It shares the `CheckTypeTag` base with the stricter
[@psalm-check-type-exact](psalm-check-type-exact-tag.md), which demands an
exact type match.

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-check-type).
