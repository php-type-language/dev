# @param-immediately-invoked-callable

<primary-label ref="phpdoc-component"/>
<secondary-label ref="phpstan-tag"/>

The `@param-immediately-invoked-callable` tag marks a callable argument that
is guaranteed to be called before the function or method returns, and is
never stored away for later. That guarantee matters to static analysis: a
variable captured by reference into the callback can be trusted to reflect
its post-call value as soon as the outer call returns, and a function that
only ever invokes such a callback synchronously is easier to reason about as
pure or side-effect-free. Without this tag, an analyzer has to assume the
callable might be kept and invoked at some unrelated point in time.

```
"@param-immediately-invoked-callable" <Variable>
    [ <Description> ]
```

> A plain marker on a mapping callback.
> ```php
> /**
>  * @param callable(int): int $mapper
>  * @param-immediately-invoked-callable $mapper
>  */
> function transform(
>     array $items,
>     callable $mapper,
> ): array
> ```

> With a description explaining why it matters here.
> ```php
> /**
>  * @param-immediately-invoked-callable $callback
>  * Runs before the lock is released, so it can
>  * safely mutate the shared buffer.
>  */
> function withLock(callable $callback): mixed
> ```

Parsing a `@param-immediately-invoked-callable` tag produces a `VariableTag`
instance exposing `$variable`: the parameter's name, without the leading
`$`. The type of the callable itself is whatever the parameter's own
[@param](param-tag.md) already declares.

```php
final class ParamImmediatelyInvokedCallableTag extends VariableTag
{
    // Adds nothing of its own; $variable is
    // declared on the parent VariableTag.
    public readonly string $variable;
}
```

The opposite case — a callable stored and invoked only after the call has
returned — is documented with
[@param-later-invoked-callable](param-later-invoked-callable-tag.md) instead.

[PHPStan documents `@param-immediately-invoked-callable`](https://phpstan.org/writing-php-code/phpdocs-basics#callables)
alongside its other callable-related tags; Psalm has no equivalent.
