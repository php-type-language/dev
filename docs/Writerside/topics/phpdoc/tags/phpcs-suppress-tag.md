# @phpcsSuppress

<primary-label ref="phpdoc-component"/>

The `@phpcsSuppress` tag is pre-3.2.0 PHP_CodeSniffer syntax for
suppressing named coding-standard checks; current PHP_CodeSniffer prefers
the `phpcs:*` comment syntax instead, but still understands the older
tag-based forms.

```
"@phpcsSuppress" <Name> [ <Description> ]
```

Parsing a `@phpcsSuppress` tag produces a `PhpcsSuppressTag` instance, carrying the
parsed `$identifier` alongside the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already provides.

```php
final class PhpcsSuppressTag extends IdentifierTag {}
```

Associated with PHP_CodeSniffer.
