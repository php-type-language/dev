# @subpackage

<primary-label ref="phpdoc-component"/>

The `@subpackage` tag subdivides a [@package](package-tag.md) into a finer
logical section, letting documentation tooling organize a large package
into smaller subsections rather than listing every element in one
undifferentiated group. It is meaningful only alongside a `@package` tag on
the same element, naming a subdivision of the package that tag already
identifies.

```
"@subpackage" [ <Description> ]
```

> Narrowing a package into a specific subsection.
> ```php
> /**
>  * @package Mail
>  * @subpackage Transport
>  */
> final class SmtpTransport {}
> ```

> With a description of what the subsection covers.
> ```php
> /**
>  * @subpackage Transport Concrete delivery mechanisms for outgoing
>  *             mail.
>  */
> ```

Parsing a `@subpackage` tag produces a `SubpackageTag` instance. Being a
pure marker, it adds nothing beyond the `$name` and optional `$description`
every [Tag](phpdoc.md#tag) already carries.

```php
final class SubpackageTag extends FlagTag {}
```

`@subpackage` is still part of phpDocumentor's current tag reference, but
phpDocumentor's own page explicitly calls it deprecated, noting that
"support may be removed in a future version" —
[phpDocumentor tag reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/subpackage.html).
It is supported here for classic-style docblocks.
