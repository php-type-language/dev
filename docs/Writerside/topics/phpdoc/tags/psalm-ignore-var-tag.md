# @psalm-ignore-var

<primary-label ref="phpdoc-component"/>

The `@psalm-ignore-var` tag excludes the `@var` annotation immediately
following it from being used by Psalm for type inference, telling the
analyzer to fall back to its own inferred type instead of trusting the
declared one.

```
"@psalm-ignore-var" [ <Description> ]
```

Parsing a `@psalm-ignore-var` tag produces a `PsalmIgnoreVarTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmIgnoreVarTag extends FlagTag {}
```
