# @require-implements

<primary-label ref="phpdoc-component"/>

Some traits are written against an interface rather than a concrete parent
class — they call methods the interface guarantees, and only make sense
mixed into a class that implements it. `@require-implements` declares that
constraint, letting a static analyzer flag any class that uses the trait
without also implementing the required interface.

```
"@require-implements" <Type> [ <Description> ]
```

> A trait that provides a default implementation
> on top of a required interface.
> ```php
> /**
>  * @require-implements Comparable
>  */
> trait SortableByDefault
> {
>     public function isGreaterThan(self $other): bool
>     {
>         return $this->compareTo($other) > 0;
>     }
> }
> ```

Parsing a `@require-implements` tag produces a `RequireImplementsTag`
instance exposing `$type` — the `TypeNode` naming the required
interface — along with the inherited `$name` and `$description`.

```php
abstract class RequireInheritanceTag extends TypedTag {}

final class RequireImplementsTag extends RequireInheritanceTag
{
    // inherits the computed TypeNode $type
    // getter from TypedTag; adds nothing.
}
```

See also [@require-extends](require-extends-tag.md) for constraining a trait
to classes extending a given parent class instead.

Neither PSR-19 nor phpDocumentor defines this tag. Its real-world
spellings are vendor-prefixed: Psalm's
[`@psalm-require-implements`](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-require-implements)
and PHPStan's `@phpstan-require-implements` (covered in
[PHPStan's PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics)).
This component recognizes the bare `@require-implements` spelling shown
above as the tool-agnostic equivalent of both.
