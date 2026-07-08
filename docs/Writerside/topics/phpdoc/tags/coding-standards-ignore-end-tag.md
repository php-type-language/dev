# @codingStandardsIgnoreEnd

<primary-label ref="phpdoc-component"/>

The `@codingStandardsIgnoreEnd` tag ends a block of code started by
[@codingStandardsIgnoreStart](coding-standards-ignore-start-tag.md),
resuming coding-standard checks from this point on. This is pre-3.2.0
PHP_CodeSniffer syntax; current PHP_CodeSniffer prefers the `phpcs:*`
comment syntax instead, but still understands it.

```
"@codingStandardsIgnoreEnd" [ <Description> ]
```

Parsing a `@codingStandardsIgnoreEnd` tag produces a `CodingStandardsIgnoreEndTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class CodingStandardsIgnoreEndTag extends FlagTag {}
```

Defined by [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Advanced-Usage#ignoring-parts-of-a-file).
