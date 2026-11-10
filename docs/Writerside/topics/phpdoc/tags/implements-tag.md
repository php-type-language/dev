# @implements

<primary-label ref="phpdoc-component"/>

The same problem `@extends` solves for a generic parent class shows up when a
class implements a generic *interface*: PHP's `implements` keyword can name
the interface but cannot supply its type parameters. `@implements` documents
those type arguments, e.g. an `ArrayAccess` implementation that is actually
keyed and valued with specific types rather than `mixed`.

```
"@implements" <Type> [ <Description> ]
```

> Documenting the key and value types of an `ArrayAccess` implementation.
> ```php
> /**
>  * @implements ArrayAccess<int, User>
>  */
> final class UserList implements ArrayAccess {}
> ```

> Implementing a generic repository interface with a concrete entity type.
> ```php
> /**
>  * @implements RepositoryInterface<Order>
>  */
> final class OrderRepository implements RepositoryInterface {}
> ```

Parsing an `@implements` tag produces a tag exposing `$type` — the `TypeNode`
naming the interface together with its type arguments — plus the `$name` and
`$description` common to every tag.

```php
final class ImplementsTag extends InheritanceTag
{
    // adds nothing of its own: the
    // `public readonly TypeNode $type` is
    // inherited from TypedTag, which in turn
    // keeps the protected TypeReference
    // $statement it was built from.
}
```

The alias [@template-implements](template-implements-tag.md) is
recognized as well; it parses to the same kind of tag as
`@implements`. See also [@extends](extends-tag.md) for the equivalent
tag used on generic parent classes.

`@implements` is not a phpDocumentor or PSR-19 tag. It is a generics
convention introduced independently by
[PHPStan](https://phpstan.org/writing-php-code/phpdocs-basics) and
[Psalm](https://psalm.dev/docs/annotating_code/templated_annotations/),
and this component follows their lead rather than any formal
standard.
