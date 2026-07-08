# @codingStandardsIgnoreFile

<primary-label ref="phpdoc-component"/>

The `@codingStandardsIgnoreFile` tag ignores the whole file for
coding-standard checks. This is pre-3.2.0 PHP_CodeSniffer syntax; current
PHP_CodeSniffer prefers the `phpcs:*` comment syntax instead, but still
understands it.

```
"@codingStandardsIgnoreFile" [ <Description> ]
```

Parsing a `@codingStandardsIgnoreFile` tag produces a `CodingStandardsIgnoreFileTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class CodingStandardsIgnoreFileTag extends FlagTag {}
```

Defined by [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Advanced-Usage#ignoring-files-and-folders).
