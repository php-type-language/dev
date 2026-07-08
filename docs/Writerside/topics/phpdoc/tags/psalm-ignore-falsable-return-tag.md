# @psalm-ignore-falsable-return

<primary-label ref="phpdoc-component"/>

The `@psalm-ignore-falsable-return` tag tells Psalm to ignore a
possible `false` value in the return type. It is defined by the
static analyzer Psalm.

```
"@psalm-ignore-falsable-return" [ <Description> ]
```

Parsing a `@psalm-ignore-falsable-return` tag produces a `PsalmIgnoreFalsableReturnTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmIgnoreFalsableReturnTag extends FlagTag {}
```
