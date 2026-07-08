# @phpstan-impure

<primary-label ref="phpdoc-component"/>

The `@phpstan-impure` tag declares a function or method as impure — that
is, having side effects — defined by PHPStan.

```
"@phpstan-impure" [ <Description> ]
```

Parsing a `@phpstan-impure` tag produces a `PhpStanImpureTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PhpStanImpureTag extends FlagTag {}
```

Documented by
[PHPStan](https://phpstan.org/writing-php-code/phpdocs-basics#impure-functions).
