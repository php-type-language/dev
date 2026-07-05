# @global

<primary-label ref="phpdoc-component"/>

The `@global` tag documents a global variable that a function or method
relies on via PHP's `global` keyword. Such a dependency is otherwise
invisible: it does not appear in the signature, so without this tag a reader
would have no way of knowing the function's behavior depends on something
outside its own parameters.

```
"@global" <Type> <Variable> [ <Description> ]
```

> Documenting a global relied upon inside the function body.
> ```php
> /**
>  * @global PDO $connection
>  */
> function fetchUsers(): array
> {
>     global $connection;
>
>     return $connection
>         ->query('SELECT * FROM users')
>         ->fetchAll();
> }
> ```

> With a description explaining how the global is used.
> ```php
> /**
>  * @global Config $config Read for the
>  *         current retry policy.
>  */
> ```

Parsing a `@global` tag produces a `GlobalTag` instance exposing `$type` —
the documented `TypeNode` — and `$variable`, the global's name without the
leading `$`. It shares this shape with [@param](param-tag.md).

```php
abstract class TypedVariableTag extends TypedTag
{
    public function __construct(
        string $name,
        TypeReference $statement,
        public readonly string $variable,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct(
            $name,
            $statement,
            $description,
        );
    }
}

// TypedTag exposes a computed getter:
//     public TypeNode $type {
//         get => $this->statement->type;
//     }

final class GlobalTag extends TypedVariableTag
{
    // Adds nothing of its own beyond what
    // TypedVariableTag already exposes.
}
```

`@global` is still part of phpDocumentor's current tag reference, but
phpDocumentor's own page says it is "not included in phpDocumentor 3.0
and may be included in a subsequent version," and discourages reliance
on globals in general —
[phpDocumentor tag reference](https://docs.phpdoc.org/guide/references/phpdoc/tags/global.html).
It remains supported here for classic-style docblocks.
