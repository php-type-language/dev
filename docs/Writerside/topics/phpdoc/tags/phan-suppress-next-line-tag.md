# @phan-suppress-next-line

<primary-label ref="phpdoc-component"/>

The `@phan-suppress-next-line` tag silences the listed issue
types reported on the next line, mirroring
[@phan-suppress-current-line](phan-suppress-current-line-tag.md)
but targeting the line that follows.

```
"@phan-suppress-next-line" <IssueName> { "," <IssueName> } [ <Description> ]
```

Parsing a `@phan-suppress-next-line` tag produces a `PhanSuppressNextLineTag` instance, carrying the
listed `$issues` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class PhanSuppressNextLineTag extends IssueListTag {}
```

Defined by [Phan](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#phan-suppress-next-line).
