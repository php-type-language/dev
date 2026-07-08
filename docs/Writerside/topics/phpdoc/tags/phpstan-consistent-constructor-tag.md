# @phpstan-consistent-constructor

<primary-label ref="phpdoc-component"/>

The `@phpstan-consistent-constructor` tag requires that all subclasses
declare a constructor compatible with the parent's, defined by PHPStan.

```
"@phpstan-consistent-constructor" [ <Description> ]
```

Parsing a `@phpstan-consistent-constructor` tag produces a `ConsistentConstructorTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class ConsistentConstructorTag extends FlagTag {}
```

The same `ConsistentConstructorTag` is produced by [@psalm-consistent-constructor](psalm-consistent-constructor-tag.md), which restate the
same concept for their respective tools.

Originates from PHPStan's own annotation vocabulary.
