# @codingStandards

<primary-label ref="phpdoc-component"/>

The `@codingStandards` tag names a coding standard for reference. This is
pre-3.2.0 PHP_CodeSniffer-era syntax; current PHP_CodeSniffer prefers the
`phpcs:*` comment syntax instead.

```
"@codingStandards" <Name> [ <Description> ]
```

Parsing a `@codingStandards` tag produces a `CodingStandardsTag` instance, carrying the
parsed `$identifier` alongside the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already provides.

```php
final class CodingStandardsTag extends IdentifierTag {}
```

Associated with PHP_CodeSniffer.
