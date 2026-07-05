# @see

<primary-label ref="phpdoc-component"/>
<secondary-label ref="inline-tag"/>

The `@see` tag points from the described element to a related one: another
class, a method, a property, a global constant, a variable, or an external
page. It is the most general of the reference tags — where
[`@uses`](uses-tag.md) and [`@used-by`](used-by-tag.md) specifically describe
a dependency relationship, `@see` only says "read also".

```
"@see" ( <Reference> | <URI> ) [ <Description> ]
```

> A reference to another method.
> ```php
> /**
>  * @see Mailer::send()
>  */
> ```

> A reference to a class constant, with a description.
> ```php
> /**
>  * @see Status::ACTIVE The only value accepted here.
>  */
> ```

> A reference to an external page.
> ```php
> /**
>  * @see https://example.com/docs/delivery
>  */
> ```

Parsing a `@see` tag produces a `SeeTag` instance exposing
`$reference`: a `CodeReference` (for a class, function, method,
constant, property or variable) or a `UriReference` (for an external
page), depending on which form was written.

```php
final class SeeTag extends ReferenceTag
{
    public function __construct(
        string $name,
        UriReference|CodeReference $reference,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct(
            $name,
            $reference,
            $description,
        );
    }
}
```

`@see` may also be used **inline**, nested inside a description as a
`{@see ...}` sequence, rather than only on its own line:

```php
/**
 * Sends the response. See {@see Mailer::send()} for the
 * transport used.
 */
```

Defined by the [PSR-19 draft `@see`
proposal](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#513-see)
and phpDocumentor's own [`@see`
reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/see.html).
