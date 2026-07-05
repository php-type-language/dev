# @category

<primary-label ref="phpdoc-component"/>

The `@category` tag assigns a package to a broad logical category, letting
documentation tooling group unrelated packages into sections such as
`Database`, `Networking`, or `Templating` when rendering an index. It is a
coarser-grained sibling of [@package](package-tag.md) and
[@subpackage](subpackage-tag.md): where those two narrow down to a specific
module, `@category` places that module within a wider organizational scheme.

```
"@category" [ <Description> ]
```

> Placing a package under a broad category.
> ```php
> /**
>  * @category Database
>  * @package Connection
>  */
> ```

> With additional context about the grouping.
> ```php
> /**
>  * @category Database Drivers and connection pooling for
>  *           relational stores.
>  */
> ```

Parsing a `@category` tag produces a `CategoryTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already carries.

```php
final class CategoryTag extends FlagTag {}
```

`@category` is still part of phpDocumentor's current tag reference, but
phpDocumentor's own page explicitly calls it deprecated, noting that
"support may be removed in a future version" —
[phpDocumentor tag reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/category.html).
It remains here for compatibility with docblocks written against the
classic toolchain.
