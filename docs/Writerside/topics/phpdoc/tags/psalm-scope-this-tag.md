# @psalm-scope-this

<primary-label ref="phpdoc-component"/>

The `@psalm-scope-this` tag binds the type of `$this` inside a
`Closure`, letting Psalm type-check the closure body as if it were
a method of the given class.

```
"@psalm-scope-this" <Type>
```

Parsing a `@psalm-scope-this` tag produces a `PsalmScopeThisTag` instance, carrying the
parsed `$type` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class PsalmScopeThisTag extends TypedTag {}
```
