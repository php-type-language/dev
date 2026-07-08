# @psalm-consistent-templates

<primary-label ref="phpdoc-component"/>

The `@psalm-consistent-templates` tag requires that all subclasses
use the same template parameters as the parent. It is defined by
the static analyzer Psalm.

```
"@psalm-consistent-templates" [ <Description> ]
```

Parsing a `@psalm-consistent-templates` tag produces a `PsalmConsistentTemplatesTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PsalmConsistentTemplatesTag extends FlagTag {}
```
