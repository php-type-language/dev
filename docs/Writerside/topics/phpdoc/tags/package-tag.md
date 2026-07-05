# @package

<primary-label ref="phpdoc-component"/>

The `@package` tag assigns an element to a logical package or module
grouping, independent of — and often predating — PHP's own namespace
system. Documentation generators use it to organize the elements they
render into sections, which is especially useful for a codebase whose
namespace layout doesn't line up with how its documentation should be
structured.

```
"@package" [ <Description> ]
```

> Grouping a class into a package.
> ```php
> /**
>  * @package Mail
>  */
> final class Mailer
> {
> }
> ```

> With a short description of the grouping.
> ```php
> /**
>  * @package Mail Everything related to
>  *          composing and sending messages.
>  */
> ```

Parsing a `@package` tag produces a `PackageTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class PackageTag extends FlagTag {}
```

For a finer-grained subdivision within a package, see
[@subpackage](subpackage-tag.md). `@package` is still part of
phpDocumentor's current tag reference and is not deprecated —
[phpDocumentor tag reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/package.html).
It is supported here for classic-style docblocks.
