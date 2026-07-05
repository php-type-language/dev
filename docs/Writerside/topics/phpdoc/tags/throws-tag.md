# @throws

<primary-label ref="phpdoc-component"/>

PHP has no native syntax at all for declaring what exceptions a function
might throw, so `@throws` is the only way to document it. Listing the
`\Throwable` types a function can raise lets callers know what is worth
catching, and lets a static analyzer flag a `catch` block that can never be
reached or a thrown type that was never declared.

```
"@throws" <Type> [ <Description> ]
```

> Documenting the exceptions raised by a single method, each with its
> own tag.
> ```php
> /**
>  * @throws NotFoundException If the user
>  *         does not exist.
>  * @throws AccessDeniedException If the
>  *         current user lacks permission.
>  */
> function fetchUser(int $id): User
> {
>     // ...
> }
> ```

Parsing a `@throws` tag produces a tag exposing `$type` — the parsed
`TypeNode` naming the thrown type — plus the `$name` and `$description`
every tag has.

```php
abstract class TypedTag extends Tag
{
    public TypeNode $type {
        get => $this->statement->type;
    }

    public function __construct(
        string $name,
        protected readonly TypeReference $statement,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct($name, $description);
    }
}

final class ThrowsTag extends TypedTag {}
```

The misspelling [@throw](throw-tag.md) is also recognized — a fairly common
typo that this component also recognizes and parses into the same kind of
tag as `@throws`.

Defined by the
[PSR-19 draft §5.15](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#515-throws)
and phpDocumentor's own
[`@throws` reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/throws.html).
