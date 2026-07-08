# @psalm-ignore-variable-method

<primary-label ref="phpdoc-component"/>

The `@psalm-ignore-variable-method` tag suppresses "undefined method"
issues for methods called on the annotated variable, letting Psalm
skip verification of a magic or dynamically resolved call.

```
"@psalm-ignore-variable-method" [ <Description> ]
```

Parsing a `@psalm-ignore-variable-method` tag produces a `PsalmIgnoreVariableMethodTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmIgnoreVariableMethodTag extends FlagTag {}
```
