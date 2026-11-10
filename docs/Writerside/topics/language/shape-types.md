# Shape Types

<show-structure for="chapter" depth="2"/>

Each composite type can be rigidly described by a structural type called a
"_shape_".

## Shape Keys

A shape within the PHP language can be applied to any array or object
and contain **implicit** keys, **explicit** keys, or both.

<compare first-title="Explicit Keys" second-title="Implicit Keys">

```php
array{
    a: First,
    42: Second,
    Other::KEY: Third,
}
```

```php
array{
    First,
    Second,
    Third,
}
```
</compare>

> **Explicit** and **implicit** keys may be used together in one shape, the
> way PHPStan and Psalm allow it. What keys the implicit fields denote is left
> to whoever reads the shape.
> ```php
> array{
>     First,
>     name: Second,
> }
> ```
{style="note"}


### Implicit Keys

An implicit field has no key at all: only its type is specified, and the
field's position within the shape determines its (numeric) index.

> ```php
> array{
>     First,
>     Second,
>     Third,
> }
> ```


### Explicit Keys

An explicit field is prefixed with a key, followed by a colon (`:`) and then
the field's type: `key: Type`. The simplest form of a key is an
[identifier](basic-types.md), the same as used for a type name: it may contain
dashes, a namespace, and the words `true`, `false` and `null` are not
literals here, but ordinary names.

> ```php
> array{
>     a: First,
>     b: Second,
> }
> ```

> ```php
> array{
>     some-key: First,
>     Some\Name: Second,
>     null: Third,
> }
> ```


### Numeric Keys

An explicit key may also be an [integer literal](literal-types.md#integer) in
any notation, including the signed ones (`-1`, `0x1F`). A float cannot be a key.

> ```php
> array{
>     42: Example,
> }
> ```

### String Keys

An explicit key may also be a [string literal](literal-types.md#strings),
which allows characters that are not permitted in an
[identifier](basic-types.md), including [escape sequences](literal-types.md#escape-sequences).

> ```php
> array{
>     "name-some": First,
>     "escape\nchars": Second,
> }
> ```

### Constant Keys

<secondary-label ref="tl1.6"/>

In addition to identifiers, numbers and strings, a field key may also be a
[class constant](const-types.md#class-constants).

<tabs>
<tab title="Examples">

> A [class constant](const-types.md#class-constants) as a field key.
> ```php
> array{
>     Path\To\ClassName::CONSTANT_NAME: string,
> }
> ```

</tab>
</tabs>

<tabs>
<tab title="Counterexamples">

> A [constant mask](const-types.md#constant-masks) cannot be a field key.
> ```php
> array{
>     Path\To\ClassName::PREFIX_*: string,
> }
> ```
>
> An error similar to the one below should occur
> ```
> Error: Invalid shape, a field, "," or "}" is expected
> ```
> {style="warning"}

</tab>
</tabs>

## Optional Fields

Specifying fields (keys) allows the optionality of the presence of a field.
Such fields are indicated by a question mark (`?`) **before** the colon (`:`) 
symbol: `key?: Type`.

<compare first-title="Optional Key" second-title="Optional Value">

```php
array{
    key?: Type,
}
```

```php
array{
    key: ?Type,
}
```
</compare>

## Unsealed Shapes

Unsealed (unclosed) shapes mean that the composite type can contain additional
fields beyond those described in the shape. Such types must be terminated with
the "`...`".

<compare first-title="Sealed" second-title="Unsealed">

```php
array{
    key: type,
}
```

```php
array{
    key: type,
    ...
}
```
</compare>


## Typed Shapes

In addition, such shapes can describe template arguments (types) for values
or for keys and values, which are described after the ellipsis (`...`) char
and contain syntax [similar to generics](generic-types.md).

<compare first-title="Without Arguments" second-title="With Arguments">

```php
array{
    user: User,
    ...
}
```

```php
array{
    user: User,
    ...<string, object>
}
```
</compare>

The ellipsis is always the last element of a shape, and a trailing comma is
allowed after any last element. A shape may consist of the ellipsis alone, and
may be empty.

> ```php
> array{...<string, object>}
> ```

> ```php
> array{}
> ```

<tabs>
<tab title="Counterexamples">

> The ellipsis must be the last.
> ```php
> array{
>     ...<string, object>,
>     user: User,
> }
> ```
>
> An error similar to the one below should occur
> ```
> Error: Invalid shape, a field, "," or "}" is expected
> ```
> {style="warning"}

> A shape and template arguments cannot be written together on a named type:
> the arguments of the shape are written after the ellipsis.
> ```php
> array<int>{user: User}
> ```
>
> An error similar to the one below should occur
> ```
> Error: Unexpected "{"
> ```
> {style="warning"}

</tab>
</tabs>
