# @phan-suppress-next-next-line

<primary-label ref="phpdoc-component"/>

The `@phan-suppress-next-next-line` tag silences the listed
issue types reported two lines below the annotation.

```
"@phan-suppress-next-next-line" <IssueName> { "," <IssueName> } [ <Description> ]
```

Parsing a `@phan-suppress-next-next-line` tag produces a `PhanSuppressNextNextLineTag` instance, carrying the
listed `$issues` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class PhanSuppressNextNextLineTag extends IssueListTag {}
```

Defined by [Phan](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code).
