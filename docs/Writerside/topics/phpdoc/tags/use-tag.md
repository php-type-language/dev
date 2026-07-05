# @use

<primary-label ref="phpdoc-component"/>

Traits can be generic too, and PHP's `use` keyword for pulling a trait into a
class carries no syntax for type arguments. `@use` supplies them, mirroring
what `@extends` does for parent classes and `@implements` does for
interfaces — for example a caching trait that needs to know what kind of
value it is caching.

```
"@use" <Type> [ <Description> ]
```

> Binding a generic caching trait's value type.
> ```php
> final class ProductRepository
> {
>     /** @use Cacheable<Product> */
>     use Cacheable;
> }
> ```

> Binding a comparable trait to the class it is mixed into.
> ```php
> final class Money
> {
>     /** @use ComparableTrait<Money> */
>     use ComparableTrait;
> }
> ```

Parsing a `@use` tag produces a tag exposing `$type` — the `TypeNode` naming
the trait with its type arguments — along with the inherited `$name` and
`$description`.

```php
final class UseTag extends InheritanceTag
{
    // extends TypedTag; adds a computed
    // TypeNode $type getter on top of
    // $name and $description.
}
```

The alias [@template-use](template-use-tag.md) is also recognized and
parses to the same `UseTag` instance. See also
[@extends](extends-tag.md) and [@implements](implements-tag.md) for the
equivalent tags used on a generic parent class or interface.

<note>
In practice, real-world PHPStan and Psalm documentation more commonly shows 
the <code>@template-use</code> spelling,
which this component recognizes as an alias.
</note>
