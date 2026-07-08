# @psalm-no-seal-methods

<primary-label ref="phpdoc-component"/>

The `@psalm-no-seal-methods` tag allows a class to declare magic
methods beyond those already documented — the inverse of
[@seal-methods](seal-methods-tag.md), which this library already
recognizes bare.

```
"@psalm-no-seal-methods" [ <Description> ]
```

Parsing a `@psalm-no-seal-methods` tag produces a `PsalmNoSealMethodsTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmNoSealMethodsTag extends FlagTag {}
```

See [Psalm's supported annotations reference](https://psalm.dev/docs/annotating_code/supported_annotations/).
