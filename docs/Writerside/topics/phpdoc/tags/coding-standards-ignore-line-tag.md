# @codingStandardsIgnoreLine

<primary-label ref="phpdoc-component"/>

The `@codingStandardsIgnoreLine` tag ignores a single line of code for
coding-standard checks, without needing a paired
[@codingStandardsIgnoreStart](coding-standards-ignore-start-tag.md) /
[@codingStandardsIgnoreEnd](coding-standards-ignore-end-tag.md) block.
This is pre-3.2.0 PHP_CodeSniffer syntax; current PHP_CodeSniffer prefers
the `phpcs:*` comment syntax instead, but still understands it.

```
"@codingStandardsIgnoreLine" [ <Description> ]
```

Parsing a `@codingStandardsIgnoreLine` tag produces a `CodingStandardsIgnoreLineTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class CodingStandardsIgnoreLineTag extends FlagTag {}
```

Defined by [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Advanced-Usage#ignoring-parts-of-a-file).
