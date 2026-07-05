# @license

<primary-label ref="phpdoc-component"/>

The `@license` tag states which license governs the element it decorates
(or, written once near the top of a file, the file as a whole). Most of the
time that means pointing at the canonical URL for the license text — an OSI
license page, a link to the project's own `LICENSE` file — but some
licenses are commonly identified by name alone, in which case the tag
carries only a description such as `MIT` or `Proprietary`.

```
"@license" ( <URL> [ <Description> ] | <Description> )
```

> Pointing at the canonical license text.
> ```php
> /**
>  * @license https://opensource.org/license/mit MIT License
>  */
> ```

> Naming the license without a URL.
> ```php
> /**
>  * @license MIT
>  */
> ```

Parsing a `@license` tag produces a `LicenseTag`
instance:

```php
final class LicenseTag extends Tag
{
    public function __construct(
        string $name,
        public ?UrlReference $url = null,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct($name, $description);
    }
}
```

It exposes `$url` — the parsed `UrlReference`, or `null`
when the license was given by name only, with the name
itself available through the tag's `$description`. Note
that `$url`, unlike almost every other exposed property
across these tags, is **not** `readonly` in the actual
source — a small inconsistency worth knowing about if
you're relying on immutability.

Defined by [phpDocumentor's own page](https://docs.phpdoc.org/guide/references/phpdoc/tags/license.html);
unlike most tags in this section, `@license` is not part
of the PSR-19 draft's tag list.
