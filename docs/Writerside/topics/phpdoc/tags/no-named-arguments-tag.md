# @no-named-arguments

<primary-label ref="phpdoc-component"/>
<secondary-label ref="phpstan-tag"/>
<secondary-label ref="psalm-tag"/>

The `@no-named-arguments` tag warns that a function or method's parameter
names are not part of its stable contract and may change in a future
release. PHP resolves named arguments (`func(name: $value)`) against the
parameter name itself, so renaming a parameter is normally a breaking
change even when the type and position stay the same; this tag tells
callers and analyzers to rely only on positional calls, so the maintainer
stays free to rename parameters without it counting as a break.

```
"@no-named-arguments" [ <Description> ]
```

> A plain marker on a method whose argument names are still in flux.
> ```php
> /**
>  * @no-named-arguments
>  */
> public function configure(string $host, int $port): void
> ```

> With a description explaining why.
> ```php
> /**
>  * @no-named-arguments Parameter names will change once the driver
>  *                     abstraction lands.
>  */
> function connect(string $dsn, ?string $user, ?string $password): PDO
> ```

Parsing a `@no-named-arguments` tag produces a `FlagTag` instance. Being a
pure marker, it adds nothing beyond the `$name` and optional `$description`
every tag already carries.

```php
final class NoNamedArgumentsTag extends FlagTag {}
```

Both [PHPStan](https://phpstan.org/writing-php-code/phpdocs-basics#no-named-arguments)
and [Psalm](https://psalm.dev/docs/annotating_code/supported_annotations/#no-named-arguments)
recognize `@no-named-arguments` in exactly this bare spelling — unlike
most of their other tags, neither vendor uses a prefixed alternative for
this one.
