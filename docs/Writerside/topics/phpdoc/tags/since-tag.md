# @since

<primary-label ref="phpdoc-component"/>

The `@since` tag documents the version in which an element first became
available. It is the opposite number of [@deprecated](deprecated-tag.md):
where that tag marks the end of an element's life, `@since` marks its
beginning, letting consumers check whether a given feature exists in the
version they depend on.

```
"@since" [ <Version> ] [ <Description> ]
```

> A method added in a later minor release.
> ```php
> /**
>  * @since 1.3 Added to support batched sends.
>  */
> public function sendMany(iterable $messages): void
> ```

> Just the version, with no further explanation needed.
> ```php
> /**
>  * @since 1.0
>  */
> ```

Parsing a `@since` tag produces a `SinceTag` instance exposing
`$version` — the version at which the element became available, or
`null` when none was given — alongside the inherited `$description`.
It shares this shape with [@deprecated](deprecated-tag.md) and
[@version](version-tag.md).

```php
abstract class VersionedTag extends Tag
{
    public function __construct(
        string $name,
        public readonly ?string $version = null,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct($name, $description);
    }
}

final class SinceTag extends VersionedTag {}
```

Defined by the [PSR-19 draft `@since`
proposal](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#514-since)
and phpDocumentor's own [`@since`
reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/since.html).
