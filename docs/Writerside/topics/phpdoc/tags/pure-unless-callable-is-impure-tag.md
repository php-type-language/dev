# @pure-unless-callable-is-impure

<primary-label ref="phpdoc-component"/>
<secondary-label ref="phpstan-tag"/>

The `@pure-unless-callable-is-impure` tag declares a function or method free
of side effects on its own, but makes that guarantee conditional on any
callable it receives as an argument. This covers higher-order helpers in the
shape of `array_map` or `array_filter`: the helper itself never touches
global state, but it simply invokes whatever callback it was handed, and
that callback might. Rather than forcing a choice between marking such a
helper unconditionally pure (wrong, if an impure callback is passed) or
unconditionally impure (too pessimistic, since most callbacks passed to it
are pure), this tag lets purity analysis defer to the callback actually
supplied at each call site.

```
"@pure-unless-callable-is-impure" [ <Description> ]
```

> A plain marker on a mapping helper.
> ```php
> /**
>  * @pure-unless-callable-is-impure
>  */
> function mapValues(
>     array $items,
>     callable $mapper,
> ): array
> ```

> With a description naming the parameter the purity depends on.
> ```php
> /**
>  * @pure-unless-callable-is-impure Purity
>  * follows whatever $reducer does.
>  */
> function reduce(
>     array $items,
>     callable $reducer,
>     mixed $initial,
> ): mixed
> ```

Parsing a `@pure-unless-callable-is-impure` tag produces a `FlagTag`
instance. Being a pure marker, it adds nothing beyond the `$name` and
optional `$description` every tag already carries.

```php
final class PureUnlessCallableIsImpureTag extends FlagTag {}
```

This is a real PHPStan tag, but it is only documented by its own
implementation, not yet covered in PHPStan's narrative docs — see the
[pull request that added support for it](https://github.com/phpstan/phpdoc-parser/pull/253).
Psalm has no equivalent.
