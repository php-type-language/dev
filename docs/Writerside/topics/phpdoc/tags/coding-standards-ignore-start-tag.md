# @codingStandardsIgnoreStart

<primary-label ref="phpdoc-component"/>

The `@codingStandardsIgnoreStart` tag starts a block of code excluded
from coding-standard checks, closed by a matching
[@codingStandardsIgnoreEnd](coding-standards-ignore-end-tag.md). This is
pre-3.2.0 PHP_CodeSniffer syntax; current PHP_CodeSniffer prefers the
`phpcs:*` comment syntax instead, but still understands it.

```
"@codingStandardsIgnoreStart" [ <Description> ]
```

Parsing a `@codingStandardsIgnoreStart` tag produces a `CodingStandardsIgnoreStartTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class CodingStandardsIgnoreStartTag extends FlagTag {}
```

Defined by [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Advanced-Usage#ignoring-parts-of-a-file).
