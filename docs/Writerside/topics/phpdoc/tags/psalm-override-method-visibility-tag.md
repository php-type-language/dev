# @psalm-override-method-visibility

<primary-label ref="phpdoc-component"/>

The `@psalm-override-method-visibility` tag allows a subclass to
override an inherited method with a different visibility than the
parent declares, without Psalm flagging the change as a violation.

<!-- generated:grammar -->
```
"@psalm-override-method-visibility" [ <Description> ]
```
<!-- /generated:grammar -->

Parsing a `@psalm-override-method-visibility` tag produces a `PsalmOverrideMethodVisibilityTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmOverrideMethodVisibilityTag extends FlagTag {}
```
