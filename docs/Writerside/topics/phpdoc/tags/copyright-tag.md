# @copyright

<primary-label ref="phpdoc-component"/>

The `@copyright` tag records who holds the copyright to an element, and
typically the year or range of years it covers. It is most often placed on a
file-level docblock or on the class that anchors a file, so that generated
documentation and license-checking tools can surface ownership information
without having to parse a separate license header.

```
"@copyright" [ <Description> ]
```

> A copyright notice naming the holder and year.
> ```php
> /**
>  * @copyright 2024 Acme Corp
>  */
> final class Mailer {}
> ```

> A range of years, used on a file that has been maintained over time.
> ```php
> /**
>  * @copyright 2018-2024 Acme Corp. All rights reserved.
>  */
> ```

Parsing a `@copyright` tag produces a `CopyrightTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already carries — the holder and year are read back out
of the free-form description text.

```php
final class CopyrightTag extends FlagTag {}
```

Defined by the [PSR-19 draft PHPDoc Tags proposal](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#53-copyright)
and [phpDocumentor's tag reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/copyright.html).
