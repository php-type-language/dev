# @staticvar

<primary-label ref="phpdoc-component"/>

The `@staticvar` tag documents the type of a `static` variable declared
inside a function or method body with PHP's `static $x = ...;` syntax. Such
a variable has no visible type annotation anywhere in the code, since PHP
has no native syntax for typing it, so without this tag its type would only
be discoverable by reading the initializer and tracing every assignment.

```
"@staticvar" <Type> [ <Variable> ]
    [ <Description> ]
```

> Documenting a static counter's type.
> ```php
> /**
>  * @staticvar int $calls
>  */
> function trackCalls(): int
> {
>     static $calls = 0;
>
>     return ++$calls;
> }
> ```

> Naming the type alone, with a description but no explicit variable.
> ```php
> /**
>  * @staticvar array Memoized results,
>  *            keyed by input hash.
>  */
> ```

Parsing a `@staticvar` tag produces a `StaticVarTag` instance exposing
`$type` — the documented `TypeNode` — and `$variable`, the variable's name
without the leading `$`, or `null` when the tag names only a type. It shares
this shape with [@var](var-tag.md).

```php
final class StaticVarTag extends TypedTag
{
    public function __construct(
        string $name,
        TypeReference $statement,
        public readonly ?string $variable = null,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct(
            $name,
            $statement,
            $description,
        );
    }
}

// TypedTag exposes a computed getter:
//     public TypeNode $type {
//         get => $this->statement->type;
//     }
```

`@staticvar` is part of the original phpDocumentor tag vocabulary, but
phpDocumentor 3 has since dropped it entirely: it is absent from the
current tag reference index, and no `tags/staticvar.html` page exists
any longer. Only the archived phpDocumentor 1.x manual still describes
it, as a legacy reference rather than current documentation —
[phpDocumentor 1.x manual, archived](https://manual.phpdoc.org/HTMLSmartyConverter/HandS/phpDocumentor/tutorial_tags.staticvar.pkg.html).
It is supported here for classic-style docblocks.
