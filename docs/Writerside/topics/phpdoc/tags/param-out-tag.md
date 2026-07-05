# @param-out

<primary-label ref="phpdoc-component"/>
<secondary-label ref="phpstan-tag"/>
<secondary-label ref="psalm-tag"/>

The `@param-out` tag documents the type held by a by-reference parameter
(`&$arg`) after the function or method has returned. A reference parameter's
own [@param](param-tag.md) describes what it accepts on the way in, but PHP
lets a function overwrite a reference with a completely different, usually
more specific, value on the way out — the classic example being a parameter
that accepts `?string` but is guaranteed to hold a non-null `string` once
the call succeeds. Without a separate tag for the outgoing type, callers and
analyzers are stuck assuming the same type applies in both directions.

```
"@param-out" <Type> <Variable> [ <Description> ]
```

> A reference parameter that starts nullable but ends up always set.
> ```php
> /**
>  * @param string|null $error
>  * @param-out string $error
>  */
> function tryParse(string $input, ?string &$error): bool
> ```

> With a description of what the narrower outgoing type represents.
> ```php
> /**
>  * @param list<int> $matches
>  * @param-out non-empty-list<int> $matches Populated only when the
>  * function returns true.
>  */
> function findAll(string $pattern, array &$matches): bool
> ```

Parsing a `@param-out` tag produces a `TypedVariableTag` instance exposing:

* `$type` — the documented `TypeNode` the reference holds after the call.
* `$variable` — the parameter's name, without the leading `$`.

```php
final class ParamOutTag extends TypedVariableTag
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

This is the same shape as [@param](param-tag.md), but describes the
outgoing value of a reference parameter rather than the incoming one.

`@param-out` originated in Psalm and was later adopted by PHPStan; the
bare spelling works in both. See
[PHPStan's coverage](https://phpstan.org/writing-php-code/phpdocs-basics#setting-parameter-type-passed-by-reference)
and
[Psalm's `@param-out`](https://psalm.dev/docs/annotating_code/supported_annotations/#param-out-psalm-param-out).
