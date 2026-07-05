# @example

<primary-label ref="phpdoc-component"/>

The `@example` tag points at an external source file that demonstrates how
to use the element it decorates, so that documentation tooling can display
a working sample alongside the API reference instead of leaving readers to
piece one together from the description alone. The reference can optionally
be narrowed to a specific line range, letting a single illustrating file be
reused across several tags that each highlight a different part of it.

```
"@example" ( <URL> | <URI> )
    [ <StartLine> [ <LinesCount> ] ]
    [ <Description> ]
```

> Pointing at an entire example file.
> ```php
> /**
>  * @example docs/examples/mailer-basic.php
>  */
> public function send(Message $message): bool
> ```

> Narrowed to the lines that show the relevant usage.
> ```php
> /**
>  * @example docs/examples/mailer-retry.php 12 8 Configuring a retry
>  *          policy.
>  */
> public function withRetries(int $attempts): static
> ```

Parsing an `@example` tag produces an `ExampleTag` instance exposing:

* `$location` — the example file's location, as a `UriReference`.
* `$start` — the starting line of the excerpt, or `null` when the whole file
  is meant.
* `$count` — the number of lines to include, or `null`; only ever set
  alongside `$start`.

```php
final class ExampleTag extends Tag
{
    public function __construct(
        string $name,
        public readonly UriReference $location,
        public readonly ?int $start = null,
        public readonly ?int $count = null,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct($name, $description);
    }
}
```

`@example` shares its line-range syntax with [@source](source-tag.md), but
the two point in opposite directions: `@example` references an external
illustrating file, while `@source` references the documented element's own
implementation.

`@example` is still part of phpDocumentor's current tag reference, not
deprecated, but its own page notes that its effects "are not yet fully
implemented in phpDocumentor 3" —
[phpDocumentor tag reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/example.html).
