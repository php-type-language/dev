# @psalm-no-seal-properties

<primary-label ref="phpdoc-component"/>

The `@psalm-no-seal-properties` tag allows a class to declare magic
properties beyond those already documented — the inverse of
[@seal-properties](seal-properties-tag.md), which this library
already recognizes bare.

```
"@psalm-no-seal-properties" [ <Description> ]
```

Parsing a `@psalm-no-seal-properties` tag produces a `PsalmNoSealPropertiesTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmNoSealPropertiesTag extends FlagTag {}
```

See [Psalm's supported annotations reference](https://psalm.dev/docs/annotating_code/supported_annotations/).
