# @param-closure-this

<primary-label ref="phpdoc-component"/>
<secondary-label ref="phpstan-tag"/>

The `@param-closure-this` tag documents what `$this` refers to inside a
`Closure` parameter. A [@param](param-tag.md) on that same parameter already
describes the closure's own signature — the arguments it receives and what
it returns — but says nothing about the object it will be bound to when the
closure is eventually invoked through `Closure::bindTo()`, `Closure::call()`,
or an equivalent internal rebinding. Without this tag, code inside the
closure that reads `$this` has no documented type to check against.

```
"@param-closure-this" <Type> <Variable> [ <Description> ]
```

> Pinning `$this` for a closure evaluated in the context of a builder.
> ```php
> /**
>  * @param Closure(): string $formatter
>  * @param-closure-this Builder $formatter
>  */
> function withFormatter(
>     Closure $formatter,
> ): static
> ```

> With a description explaining the binding.
> ```php
> /**
>  * @param Closure(int): void $callback
>  * @param-closure-this Connection $callback
>  * Bound before dispatch so the callback can
>  * reach the connection's protected members.
>  */
> function onConnect(Closure $callback): void
> ```

Parsing a `@param-closure-this` tag produces a `TypedVariableTag` instance
exposing:

* `$type` — the documented `TypeNode` that `$this` is bound to.
* `$variable` — the closure parameter's name, without the leading `$`.

```php
final class ParamClosureThisTag extends TypedVariableTag
{
    // Own constructor adds nothing; both
    // properties below are inherited from
    // TypedVariableTag (and its parent TypedTag):

    public TypeNode $type {
        get => $this->statement->type;
    }

    public readonly string $variable;
}
```

This is the same shape as [@param](param-tag.md), just aimed at the
closure's binding rather than its signature.

[PHPStan documents `@param-closure-this`](https://phpstan.org/writing-php-code/phpdocs-basics#callables)
alongside its other callable-related tags; Psalm has no equivalent.
