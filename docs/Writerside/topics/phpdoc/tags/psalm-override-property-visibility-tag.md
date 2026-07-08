# @psalm-override-property-visibility

<primary-label ref="phpdoc-component"/>

The `@psalm-override-property-visibility` tag allows a subclass to
override an inherited property with a different visibility than the
parent declares, without Psalm flagging the change as a violation.

```
"@psalm-override-property-visibility" [ <Description> ]
```

Parsing a `@psalm-override-property-visibility` tag produces a `PsalmOverridePropertyVisibilityTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmOverridePropertyVisibilityTag extends FlagTag {}
```
