# @psalm-check-type-exact

<primary-label ref="phpdoc-component"/>

The `@psalm-check-type-exact` tag asserts that a variable resolves
to exactly the given type, disallowing narrower or wider matches.
It is defined by the static analyzer Psalm, alongside the looser
[@psalm-check-type](psalm-check-type-tag.md).

```
"@psalm-check-type-exact" <Variable> "=" <Type>
```

Parsing a `@psalm-check-type-exact` tag produces a `PsalmCheckTypeExactTag`
instance, carrying the checked `$variable` and its expected `$type` alongside
the `$name` every [Tag](phpdoc.md#tag) already provides.

```php
final class PsalmCheckTypeExactTag extends CheckTypeTag {}
```

It shares the `CheckTypeTag` base with the looser
[@psalm-check-type](psalm-check-type-tag.md), which allows assignable matches.

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/) page.
