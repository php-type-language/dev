# @seal-methods

<primary-label ref="phpdoc-component"/>
<secondary-label ref="psalm-tag"/>

The `@seal-methods` tag closes off a class's `__call()`/`__callStatic()`
surface to exactly the magic methods already declared with
[@method](method-tag.md), nothing more. A class documenting several magic
methods normally leaves the door open for an analyzer to assume `__call()`
might answer to other, undocumented method names too; writing
`@seal-methods` on the class tells the analyzer that assumption is wrong,
so a call to any magic method not already listed can be reported as an
error instead of silently allowed through.

```
"@seal-methods" [ <Description> ]
```

> Closing the magic method surface of a fluent builder.
> ```php
> /**
>  * @method static setName(string $name)
>  * @method static setAge(int $age)
>  * @seal-methods
>  */
> class Builder {}
> ```

> With a description explaining the intent.
> ```php
> /**
>  * @method string getName()
>  * @seal-methods No further magic methods will ever be added here.
>  */
> class ReadOnlyModel {}
> ```

Parsing a `@seal-methods` tag produces a `FlagTag` instance. Being a pure
marker, it adds nothing beyond the `$name` and optional `$description`
every tag already carries.

```php
final class SealMethodsTag extends FlagTag {}
```

The equivalent tag for magic properties declared through
[@property](property-tag.md) is
[@seal-properties](seal-properties-tag.md).

This is a Psalm-only convention: Psalm's own vocabulary is
`@psalm-seal-methods`/`@psalm-no-seal-methods`, but its docs explicitly
list the bare `@seal-methods`/`@no-seal-methods` spellings as equivalent.
[See Psalm's reference](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-seal-methods-psalm-no-seal-methods-seal-methods-no-seal-methods).
PHPStan has no equivalent concept.
