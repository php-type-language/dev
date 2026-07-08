# @phan-constructor-used-for-side-effects

<primary-label ref="phpdoc-component"/>

The `@phan-constructor-used-for-side-effects` tag declares that a
constructor's return value is intentionally discarded by callers,
suppressing the issue Phan would otherwise raise for an unused `new`
expression.

```
"@phan-constructor-used-for-side-effects" [ <Description> ]
```

Parsing a `@phan-constructor-used-for-side-effects` tag produces a `PhanConstructorUsedForSideEffectsTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PhanConstructorUsedForSideEffectsTag extends FlagTag {}
```

See [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
