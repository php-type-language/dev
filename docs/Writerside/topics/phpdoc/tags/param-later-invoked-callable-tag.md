# @param-later-invoked-callable

<primary-label ref="phpdoc-component"/>
<secondary-label ref="phpstan-tag"/>

The `@param-later-invoked-callable` tag marks a callable argument that is
kept by the function or method and invoked only after the call has already
returned — an event listener being registered, a callback stashed for a
future tick of an event loop, and similar patterns. This matters to static
analysis for the opposite reason
[@param-immediately-invoked-callable](param-immediately-invoked-callable-tag.md)
does: variables captured by reference into the callback cannot be trusted
to hold any particular value once the outer call returns, since the
callback body has not actually run yet, and the function storing it cannot
be assumed pure just because the callback itself might be.

```
"@param-later-invoked-callable" <Variable>
    [ <Description> ]
```

> A plain marker on a listener registration.
> ```php
> /**
>  * @param callable(Event): void $listener
>  * @param-later-invoked-callable $listener
>  */
> function on(string $event, callable $listener): void
> ```

> With a description explaining when it actually runs.
> ```php
> /**
>  * @param-later-invoked-callable $callback
>  * Invoked once the response arrives, which
>  * may be long after this method returns.
>  */
> function then(callable $callback): static
> ```

Parsing a `@param-later-invoked-callable` tag produces a `VariableTag`
instance exposing `$variable`: the parameter's name, without the leading
`$`. The type of the callable itself is whatever the parameter's own
[@param](param-tag.md) already declares.

```php
final class ParamLaterInvokedCallableTag extends VariableTag
{
    // Adds nothing of its own; $variable is
    // declared on the parent VariableTag.
    public readonly string $variable;
}
```

The opposite case — a callable guaranteed to run before the call returns —
is documented with
[@param-immediately-invoked-callable](param-immediately-invoked-callable-tag.md)
instead.

[PHPStan documents `@param-later-invoked-callable`](https://phpstan.org/writing-php-code/phpdocs-basics#callables)
alongside its other callable-related tags; Psalm has no equivalent.
