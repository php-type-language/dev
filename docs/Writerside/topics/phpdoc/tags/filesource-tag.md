# @filesource

<primary-label ref="phpdoc-component"/>

The `@filesource` tag tells documentation tooling to embed a full,
syntax-highlighted copy of the current file's source code alongside the
documentation generated for it. It is typically placed once, in the
file-level docblock near the top of the file, rather than repeated on
individual elements.

```
"@filesource" [ <Description> ]
```

> Requesting the source listing for an entire file.
> ```php
> /**
>  * Mailer transport implementations.
>  *
>  * @filesource
>  */
> ```

> With a description explaining why the listing is useful here.
> ```php
> /**
>  * @filesource Reference implementation;
>  *             read alongside the interface.
>  */
> ```

Parsing a `@filesource` tag produces a `FilesourceTag` instance. Being a
pure marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class FilesourceTag extends FlagTag {}
```

This tag is still part of phpDocumentor's current tag reference and is
not deprecated —
[phpDocumentor tag reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/filesource.html).
It is parsed here for compatibility with classic-style docblocks.
