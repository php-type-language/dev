# @psalm-assert-untainted

<primary-label ref="phpdoc-component"/>

The `@psalm-assert-untainted` tag asserts that the given variable holds no
tainted data from this point on. It is part of Psalm's taint-analysis
assertion family, alongside [@psalm-assert](psalm-assert-tag.md).

```
"@psalm-assert-untainted" <Variable> [ <Description> ]
```

> Asserting that a value is safe after sanitization.
> ```php
> /**
>  * @psalm-assert-untainted $input
>  */
> function sanitize(string $input): string {}
> ```

Parsing a `@psalm-assert-untainted` tag produces a
`PsalmAssertUntaintedTag` instance, carrying the asserted `$variable`
alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class PsalmAssertUntaintedTag extends VariableTag {}
```

See [Psalm's security analysis annotations](https://psalm.dev/docs/security_analysis/annotations/).
