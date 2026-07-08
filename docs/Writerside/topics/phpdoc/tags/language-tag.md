# @language

<primary-label ref="phpdoc-component"/>

The `@language` tag injects a foreign-language grammar — SQL, HTML,
regular expressions, and the like — into a string literal, so PhpStorm can
apply the right syntax highlighting and completion inside it.

```
"@language" <Name> [ <Description> ]
```

Parsing a `@language` tag produces a `LanguageTag` instance, carrying the
parsed `$identifier` alongside the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already provides.

```php
final class LanguageTag extends IdentifierTag {}
```

Associated with PhpStorm; no dedicated JetBrains page documenting this
exact doc-comment tag could be confirmed.
