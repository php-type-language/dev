# @phpstan-ignore

<primary-label ref="phpdoc-component"/>

The `@phpstan-ignore` tag silences the listed error identifiers reported
on the current line, defined by PHPStan.

```
"@phpstan-ignore" <Name> { "," <Name> }
    [ <Description> ]
```

Parsing a `@phpstan-ignore` tag produces a `PhpStanIgnoreTag` instance, carrying the
listed `$issues` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class PhpStanIgnoreTag extends IssueListTag {}
```

Documented by
[PHPStan](https://phpstan.org/user-guide/ignoring-errors#ignoring-in-code-using-phpdocs).
