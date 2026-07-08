# @phan-suppress-current-line

<primary-label ref="phpdoc-component"/>

The `@phan-suppress-current-line` tag silences the listed
issue types reported on the current line.

```
"@phan-suppress-current-line" <Name>
    { "," <Name> } [ <Description> ]
```

Parsing a `@phan-suppress-current-line` tag produces a `PhanSuppressCurrentLineTag` instance, carrying the
listed `$issues` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class PhanSuppressCurrentLineTag extends IssueListTag {}
```

Defined by [Phan](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#phan-suppress-current-line).
