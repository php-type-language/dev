# @author

<primary-label ref="phpdoc-component"/>

The `@author` tag records who wrote or maintains the element it decorates.
It is purely informational — nothing in the type system reads it — but it
gives readers (and tooling that generates contributor listings or contact
information) a place to find out who to ask about a class, method, or
function.

```
"@author" <AuthorName> [ "<" <Email> ">" ] [ <Description> ]
```

> Just a name, with no way to contact them directly.
> ```php
> /**
>  * @author Jane Doe
>  */
> ```

> A name with an email address for follow-up questions.
> ```php
> /**
>  * @author Jane Doe <jane.doe@example.com>
>  */
> ```

Parsing an `@author` tag produces an `AuthorTag` instance exposing:

* `$author` — the name as written.
* `$email` — the address inside the angle brackets, or `null` when none was
  given.

```php
final class AuthorTag extends Tag
{
    public function __construct(
        string $name,
        public readonly string $author,
        public readonly ?string $email = null,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct($name, $description);
    }
}
```

Multiple `@author` tags may appear on the same element when several people
share responsibility for it; each one is parsed independently.

Defined by the [PSR-19 draft PHPDoc Tags proposal](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#52-author)
and [phpDocumentor's tag reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/author.html).
