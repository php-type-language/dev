# @psalm-ignore-nullable-return

<primary-label ref="phpdoc-component"/>

The `@psalm-ignore-nullable-return` tag tells Psalm to ignore a
possible `null` value in the return type. It is defined by the
static analyzer Psalm.

```
"@psalm-ignore-nullable-return" [ <Description> ]
```

Parsing a `@psalm-ignore-nullable-return` tag produces a `PsalmIgnoreNullableReturnTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmIgnoreNullableReturnTag extends FlagTag {}
```
