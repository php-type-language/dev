# @require-extends

<primary-label ref="phpdoc-component"/>

A trait can be written to depend on the class it will eventually be mixed
into — calling methods or reading properties that the trait itself never
declares. `@require-extends` makes that dependency explicit: it states that
the trait may only be used inside a class extending the given type, so a
static analyzer can verify every such use site and let the trait reference
the parent's members without redeclaring or stubbing them.

```
"@require-extends" <Type> [ <Description> ]
```

> A trait that assumes it is only ever mixed into
> an `Entity` subclass.
> ```php
> /**
>  * @require-extends Entity
>  */
> trait SoftDeletable
> {
>     public function delete(): void
>     {
>         $this->deletedAt = new DateTimeImmutable();
>     }
> }
> ```

Parsing a `@require-extends` tag produces a `RequireExtendsTag`
instance exposing `$type` — the `TypeNode` naming the required
parent class — plus the `$name` and `$description` every tag
carries.

```php
abstract class RequireInheritanceTag extends TypedTag {}

final class RequireExtendsTag extends RequireInheritanceTag
{
    // inherits the computed TypeNode $type
    // getter from TypedTag; adds nothing.
}
```

See also [@require-implements](require-implements-tag.md) for constraining a
trait to classes implementing a given interface instead.

Neither PSR-19 nor phpDocumentor defines this tag. Its real-world
spellings are vendor-prefixed: Psalm's
[`@psalm-require-extends`](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-require-extends)
and PHPStan's `@phpstan-require-extends` (covered in
[PHPStan's PHPDoc basics](https://phpstan.org/writing-php-code/phpdocs-basics)).
This component recognizes the bare `@require-extends` spelling shown
above as the tool-agnostic equivalent of both.
