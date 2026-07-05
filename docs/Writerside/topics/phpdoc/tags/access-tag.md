# @access

<primary-label ref="phpdoc-component"/>

The `@access` tag documents the visibility of the element it decorates. It
dates from a time before PHP allowed `public`, `protected`, and `private`
modifiers on every kind of class member, when a docblock was the only place
some elements' visibility could be recorded at all. Native modifiers have
made it largely redundant for ordinary methods and properties, but it still
turns up to describe a magic member or to override how visible an element
appears in generated documentation.

```
"@access"
    ( "public" | "protected" | "private" )
    [ <Description> ]
```

> Documenting a magic property as effectively private.
> ```php
> /**
>  * @property string $token
>  * @access private
>  */
> class Session
> {
> }
> ```

> With a description explaining the reasoning.
> ```php
> /**
>  * @access protected Only subclasses should
>  *         read the raw buffer.
>  */
> ```

Parsing an `@access` tag produces an `AccessTag` instance exposing
`$access`, one of the `Visibility::Public`, `Visibility::Protected`, or
`Visibility::Private` enum cases matching the keyword that was written.

```php
enum Visibility: string
{
    case Public = 'public';
    case Protected = 'protected';
    case Private = 'private';
}

final class AccessTag extends Tag
{
    public function __construct(
        string $name,
        public readonly Visibility $access,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct($name, $description);
    }
}
```

`@access` is part of the original phpDocumentor tag vocabulary, but
phpDocumentor 3 has since dropped it entirely: it is absent from the
current tag reference index, and no `tags/access.html` page exists any
longer. Only the archived phpDocumentor 1.x manual still describes it,
as a legacy reference rather than current documentation —
[phpDocumentor 1.x manual, archived](https://manual.phpdoc.org/HTMLSmartyConverter/HandS/phpDocumentor/tutorial_tags.access.pkg.html).
It is supported here purely for reading older, classic-style docblocks.
