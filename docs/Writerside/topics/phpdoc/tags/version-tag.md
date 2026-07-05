# @version

<primary-label ref="phpdoc-component"/>

The `@version` tag documents the current version of the element it
annotates, most often placed once on a file or class to record which
release it corresponds to. This differs from [@since](since-tag.md), which
records when a particular feature was first introduced: `@version` names
what the element currently is, not when any part of it arrived.

```
"@version" [ <Version> ] [ <Description> ]
```

> Tagging a class with the release it ships in.
> ```php
> /**
>  * @version 1.4.2
>  */
> final class Mailer
> {
> }
> ```

> With a short description alongside the version.
> ```php
> /**
>  * @version 2.0.0-beta.1 Transport
>  *     negotiation is not yet finalized.
>  */
> ```

Parsing a `@version` tag produces a `VersionTag` instance exposing
`$version` — the current version of the element, or `null` when none was
given — alongside the inherited `$description`. It shares this shape with
[@since](since-tag.md) and [@deprecated](deprecated-tag.md).

```php
final class VersionTag extends VersionedTag
{
    // extends VersionedTag; adds nothing
    // beyond the inherited $version,
    // $name and $description.
}
```

Defined by the PSR-19 draft
([FIG proposed PHPDoc Tags, §5.19](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#519-version))
and by
[phpDocumentor](https://docs.phpdoc.org/guide/references/phpdoc/tags/version.html).
