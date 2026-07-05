# @extends

<primary-label ref="phpdoc-component"/>

When a class extends a generic parent — one that declares its own
`@template` type parameters — plain PHP inheritance has no
way to say what those parameters resolve to in the subclass. `@extends` fills
that gap by supplying the concrete (or still-generic) type arguments for the
parent, so that a static analyzer can follow the type all the way down the
hierarchy.

```
"@extends" <Type> [ <Description> ]
```

> Binding a generic collection's item type to a concrete class.
> ```php
> /**
>  * @extends Collection<User>
>  */
> final class UserCollection extends Collection
> {
> }
> ```

> Forwarding a still-open type parameter to a further subclass.
> ```php
> /**
>  * @template T
>  * @extends Repository<T>
>  */
> abstract class BaseRepository extends Repository
> {
> }
> ```

Parsing an `@extends` tag produces a tag exposing `$type` — the parsed
`TypeNode` describing the parent with its type arguments — alongside the
`$name` and `$description` every tag carries.

```php
final class ExtendsTag extends TypedTag
{
    // adds nothing of its own: $type is inherited
    // from TypedTag as a computed getter:
    //
    //     public TypeNode $type {
    //         get => $this->statement->type;
    //     }
    //
    // backed by a protected TypeReference
    // $statement, which is not itself part
    // of the public shape.
}
```

This component also registers two alternative spellings,
[@inherits](inherits-tag.md) and
[@template-extends](template-extends-tag.md), that some tools use in
place of `@extends`; parsing either one produces the exact same kind
of tag. See also [@implements](implements-tag.md) and
[@use](use-tag.md) for the equivalent tags on interfaces and traits.

`@extends` is not a phpDocumentor or PSR-19 tag. It is a generics
convention introduced independently by
[PHPStan](https://phpstan.org/writing-php-code/phpdocs-basics) and
[Psalm](https://psalm.dev/docs/annotating_code/templated_annotations/),
and this component follows their lead rather than any formal
standard.
