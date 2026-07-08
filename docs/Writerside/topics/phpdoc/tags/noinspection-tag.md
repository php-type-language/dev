# @noinspection

<primary-label ref="phpdoc-component"/>

The `@noinspection` tag suppresses one or more named IDE inspections for
the element that follows it, so PhpStorm stops flagging a specific,
intentional pattern in that spot.

```
"@noinspection" <Name> [ <Description> ]
```

Parsing a `@noinspection` tag produces a `NoinspectionTag` instance, carrying the
parsed `$identifier` alongside the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already provides.

```php
final class NoinspectionTag extends IdentifierTag {}
```

Defined by [PhpStorm](https://www.jetbrains.com/help/phpstorm/disabling-and-enabling-inspections.html).
