# @phpstan-ignore-line

<primary-label ref="phpdoc-component"/>

The `@phpstan-ignore-line` tag silences any error reported on the current
line, defined by PHPStan. Unlike [@phpstan-ignore](phpstan-ignore-tag.md),
it does not name specific error identifiers.

```
"@phpstan-ignore-line" [ <Description> ]
```

Parsing a `phpstan-ignore-line` tag produces a `PhpStanIgnoreLineTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PhpStanIgnoreLineTag extends FlagTag {}
```

Documented by
[PHPStan](https://phpstan.org/user-guide/ignoring-errors#ignoring-in-code-using-phpdocs).
