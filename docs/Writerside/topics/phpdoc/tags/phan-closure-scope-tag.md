# @phan-closure-scope

<primary-label ref="phpdoc-component"/>

The `@phan-closure-scope` tag binds the type of `$this` inside a
`Closure`, letting Phan analyze the closure's body as though it were
bound to an instance of the given class.

```
"@phan-closure-scope" <Type>
```

Parsing a `@phan-closure-scope` tag produces a `PhanClosureScopeTag` instance, carrying the
parsed `$type` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class PhanClosureScopeTag extends TypedTag {}
```

Defined by [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#phan-closure-scope).
