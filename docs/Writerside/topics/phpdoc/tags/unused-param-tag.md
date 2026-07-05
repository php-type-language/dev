# @unused-param

<primary-label ref="phpdoc-component"/>

The `@unused-param` tag marks a single argument as intentionally left
unread inside the function body — for example, a parameter that exists only
to satisfy an interface or callback signature the implementation does not
actually need. Static analyzers commonly warn about unused parameters as a
sign of a mistake, but that warning is a false positive whenever the
parameter is unused on purpose; rather than disabling the check for the
whole function, this tag suppresses it for exactly the one parameter named.

```
"@unused-param" <Variable> [ <Description> ]
```

> Silencing the warning for an interface parameter the implementation
> ignores.
> ```php
> /**
>  * @unused-param $ctx
>  */
> public function handle(Request $request, array $ctx): Response
> ```

> With a description explaining why the argument is unused.
> ```php
> /**
>  * @unused-param $previous This listener never needs the old value.
>  */
> function onChange(mixed $current, mixed $previous): void
> ```

Parsing an `@unused-param` tag produces a `VariableTag` instance exposing
`$variable`: the parameter's name, without the leading `$`.

```php
final class UnusedParamTag extends VariableTag
{
    // Adds nothing of its own; $variable is
    // declared on the parent VariableTag.
    public readonly string $variable;
}
```
