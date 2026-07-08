# @psalm-consistent-constructor

<primary-label ref="phpdoc-component"/>

The `@psalm-consistent-constructor` tag requires that all subclasses
declare a constructor compatible with the parent's. It is defined
by the static analyzer Psalm.

```
"@psalm-consistent-constructor" [ <Description> ]
```

Parsing a `@psalm-consistent-constructor` tag produces a `ConsistentConstructorTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class ConsistentConstructorTag extends FlagTag {}
```

The same `ConsistentConstructorTag` is produced by [@phpstan-consistent-constructor](phpstan-consistent-constructor-tag.md), which restate the
same concept for their respective tools.

See [Psalm's supported annotations](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-consistent-constructor).
