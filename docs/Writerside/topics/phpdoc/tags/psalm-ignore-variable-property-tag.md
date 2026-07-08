# @psalm-ignore-variable-property

<primary-label ref="phpdoc-component"/>

The `@psalm-ignore-variable-property` tag suppresses "undefined
property" issues for properties accessed on the annotated variable,
letting Psalm skip verification of a magic or dynamically resolved
property access.

```
"@psalm-ignore-variable-property" [ <Description> ]
```

Parsing a `@psalm-ignore-variable-property` tag produces a `PsalmIgnoreVariablePropertyTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmIgnoreVariablePropertyTag extends FlagTag {}
```
