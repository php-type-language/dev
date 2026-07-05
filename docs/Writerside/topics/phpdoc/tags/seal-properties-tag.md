# @seal-properties

<primary-label ref="phpdoc-component"/>
<secondary-label ref="psalm-tag"/>

The `@seal-properties` tag closes off a class's magic property surface to
exactly the properties already declared with
[@property](property-tag.md), [@property-read](property-read-tag.md), or
[@property-write](property-write-tag.md), nothing more. Without it, an
analyzer that sees `__get()`/`__set()` implemented has to assume any
property name might resolve at runtime, even one never mentioned in the
docblock; `@seal-properties` removes that uncertainty, so access to an
undocumented magic property can be flagged as an error instead of passing
silently.

```
"@seal-properties" [ <Description> ]
```

> Closing the magic property surface of a data object.
> ```php
> /**
>  * @property-read int $id
>  * @property string $name
>  * @seal-properties
>  */
> class Record {}
> ```

> With a description explaining the intent.
> ```php
> /**
>  * @property-read DateTimeImmutable $createdAt
>  * @seal-properties Every magic property this
>  * class exposes is listed above.
>  */
> class Entry {}
> ```

Parsing a `@seal-properties` tag produces a `FlagTag` instance. Being a
pure marker, it adds nothing beyond the `$name` and optional
`$description` every tag already carries.

```php
final class SealPropertiesTag extends FlagTag {}
```

The equivalent tag for magic methods declared through
[@method](method-tag.md) is [@seal-methods](seal-methods-tag.md).

This is a Psalm-only convention, following the same pattern as
`@seal-methods`: Psalm's own vocabulary is
`@psalm-seal-properties`/`@psalm-no-seal-properties`, with the bare
`@seal-properties`/`@no-seal-properties` spellings documented as
equivalent.
[See Psalm's reference](https://psalm.dev/docs/annotating_code/supported_annotations/#psalm-seal-properties-psalm-no-seal-properties-seal-properties-no-seal-properties).
PHPStan has no equivalent concept.
