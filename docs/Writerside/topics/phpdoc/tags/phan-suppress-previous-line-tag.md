# @phan-suppress-previous-line

<primary-label ref="phpdoc-component"/>

The `@phan-suppress-previous-line` tag silences the listed
issue types reported on the previous line.

```
"@phan-suppress-previous-line" <IssueName> { "," <IssueName> } [ <Description> ]
```

Parsing a `@phan-suppress-previous-line` tag produces a `PhanSuppressPreviousLineTag` instance, carrying the
listed `$issues` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class PhanSuppressPreviousLineTag extends IssueListTag {}
```

Defined by [Phan](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
