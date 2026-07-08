# @phan-output-reference

<primary-label ref="phpdoc-component"/>

The `@phan-output-reference` tag marks a by-reference argument as
output-only — Phan's own counterpart to
[@param-out](param-out-tag.md).

```
"@phan-output-reference" [ <Description> ]
```

Parsing a `@phan-output-reference` tag produces a `PhanOutputReferenceTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PhanOutputReferenceTag extends FlagTag {}
```

Defined by [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#phan-output-reference).
