# @var

<primary-label ref="phpdoc-component"/>

`@var` is the most flexible of the type tags: it documents the type of a
property, a class constant, or — written inline, right above an assignment
— a local variable at the point it is declared or reassigned. That last
form is especially useful for narrowing a variable's type where PHP or a
static analyzer would otherwise infer something too broad, such as
right after decoding JSON or pulling a value out of an untyped array.

```
"@var" <Type> [ <Variable> ] [ <Description> ]
```

The variable name is optional and mainly exists to disambiguate which of
several variables mentioned on the same line the type applies to; on a
property or constant, where there is only ever one target, it is normally
left out entirely.

> Documenting a property's type.
> ```php
> class Model
> {
>     /** @var list<string> */
>     private array $tags;
> }
> ```

> Narrowing an inline local variable's inferred type.
> ```php
> /** @var User $user */
> $user = $session->get('user');
> ```

> Naming the variable to disambiguate a multi-assignment line.
> ```php
> /** @var User $user */
> [$user, $token] = $authenticator
>     ->resolve();
> ```

Parsing a `@var` tag produces a `VarTag` instance exposing:

* `$type` — the documented `TypeNode`.
* `$variable` — the name after the type, without the leading `$`, or `null`
  when none was written. Unlike [@param](param-tag.md), whose `$variable`
  is always present, `@var`'s is nullable because the name is optional in
  the grammar.

```php
final class VarTag extends TypedTag
{
    public function __construct(
        string $name,
        TypeReference $statement,
        public readonly ?string $variable = null,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct(
            $name,
            $statement,
            $description,
        );
    }
}
```

<note>
PHPStan and Psalm additionally recognize a vendor-prefixed
<code>@phpstan-var</code> / <code>@psalm-var</code> spelling. This
component parses only the plain <code>@var</code> tag.
</note>

Defined by the PSR-19 draft
([FIG proposed PHPDoc Tags, §5.18](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#518-var))
and by
[phpDocumentor](https://docs.phpdoc.org/guide/references/phpdoc/tags/var.html).
