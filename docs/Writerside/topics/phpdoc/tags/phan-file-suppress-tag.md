# @phan-file-suppress

<primary-label ref="phpdoc-component"/>

The `@phan-file-suppress` tag silences the listed issue types for
the whole file it appears in, rather than just a single element or
line.

```
"@phan-file-suppress" <Name> { "," <Name> } [ <Description> ]
```

Parsing a `@phan-file-suppress` tag produces a `PhanFileSuppressTag` instance, carrying the
listed `$issues` alongside the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already provides.

```php
final class PhanFileSuppressTag extends IssueListTag {}
```

Defined by [Phan's Annotating Your Source Code wiki](https://github.com/phan/phan/wiki/Annotating-Your-Source-Code#phan-file-suppress).
