# @name

<primary-label ref="phpdoc-component"/>

The `@name` tag assigns an alternate name to a procedural (non-class) page
or a global variable, so that documentation tooling and other tags can refer
to it by that alias rather than by its actual, often awkward, identifier.
It is mainly useful for old-style procedural PHP code written before
namespaces and classes were the norm, where two files might otherwise define
identically-named globals or functions that generated documentation would
struggle to tell apart.

```
"@name" <Name> [ <Description> ]
```

> Giving a global configuration array a clearer alias.
> ```php
> /**
>  * @global array $GLOBALS['config']
>  * @name config
>  */
> ```

> With a description explaining the alias.
> ```php
> /**
>  * @name legacy_mailer Superseded by Mailer::class; kept for BC.
>  */
> ```

Parsing a `@name` tag produces a `NameTag` instance exposing `$alias`, the
name assigned to the element, as a string.

```php
final class NameTag extends Tag
{
    public function __construct(
        string $name,
        public readonly string $alias,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct($name, $description);
    }
}
```

`@name` is part of the original phpDocumentor tag vocabulary, but
phpDocumentor 3 has since dropped it entirely: it is absent from the
current tag reference index, and no `tags/name.html` page exists any
longer. Only the archived phpDocumentor 1.x manual still describes it,
as a legacy reference rather than current documentation —
[phpDocumentor 1.x manual, archived](https://manual.phpdoc.org/HTMLSmartyConverter/HandS/phpDocumentor/tutorial_tags.name.pkg.html).
It is parsed here only for compatibility with classic-style docblocks.
