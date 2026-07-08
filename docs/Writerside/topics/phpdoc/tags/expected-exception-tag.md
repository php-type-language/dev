# @expectedException

<primary-label ref="phpdoc-component"/>

The `@expectedException` tag declares the `Throwable` a test method is
expected to throw. It is an old PHPUnit-era convention that predates the
`expectException()` method call and is unrelated to PhpStorm itself.

```
"@expectedException" <Type> [ <Description> ]
```

Parsing a `@expectedException` tag produces a `ExpectedExceptionTag` instance, carrying the
parsed `$type` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class ExpectedExceptionTag extends TypedTag {}
```

A historical PHPUnit convention, not a PhpStorm tag.
