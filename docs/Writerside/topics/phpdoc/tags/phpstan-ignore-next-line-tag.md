# @phpstan-ignore-next-line

<primary-label ref="phpdoc-component"/>

The `@phpstan-ignore-next-line` tag silences any error reported on the
next line, defined by PHPStan. It behaves like
[@phpstan-ignore-line](phpstan-ignore-line-tag.md), aimed one line ahead.

```
"@phpstan-ignore-next-line" [ <Description> ]
```

Parsing a `phpstan-ignore-next-line` tag produces a `PhpStanIgnoreNextLineTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PhpStanIgnoreNextLineTag extends FlagTag {}
```

Documented by
[PHPStan](https://phpstan.org/user-guide/ignoring-errors#ignoring-in-code-using-phpdocs).
