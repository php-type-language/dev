# @source

<primary-label ref="phpdoc-component"/>

The `@source` tag points at a range of lines within the documented element's
own source code, so that documentation tooling can render an excerpt of the
real implementation next to the docblock — useful when seeing the actual
code clarifies behavior that prose alone would need many words to explain.
Unlike [@example](example-tag.md), which references a separate illustrating
file, `@source` always refers back to the element it is written on.

```
"@source" <StartLine> [ <LinesCount> ]
    [ <Description> ]
```

> Excerpting a specific block of the method's own body.
> ```php
> /**
>  * @source 4 6
>  */
> public function normalize(string $value): string
> {
>     // ...
> }
> ```

> From a given line through the rest of the element, with a description.
> ```php
> /**
>  * @source 10 The core resolution algorithm starts here.
>  */
> ```

Parsing a `@source` tag produces a `SourceTag` instance exposing `$start` —
the starting line of the excerpt, always given — and `$count`, the number of
lines to include, or `null` when the excerpt runs to the end of the element.

```php
final class SourceTag extends Tag
{
    public function __construct(
        string $name,
        public readonly int $start,
        public readonly ?int $count = null,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct($name, $description);
    }
}
```

`@source` is still part of phpDocumentor's current tag reference, but its
own page notes that its effects "are not yet fully implemented in
phpDocumentor 3" —
[phpDocumentor tag reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/source.html).
It remains here for reading classic-style docblocks.
