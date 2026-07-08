# @psalm-variadic

<primary-label ref="phpdoc-component"/>

The `@psalm-variadic` tag declares that a class's magic
`__call`/`__callStatic` methods accept a variadic list of arguments.

```
"@psalm-variadic" [ <Description> ]
```

Parsing a `@psalm-variadic` tag produces a `PsalmVariadicTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmVariadicTag extends FlagTag {}
```
