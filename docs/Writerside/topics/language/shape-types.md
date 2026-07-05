# Shape Types

<show-structure for="chapter" depth="2"/>

Each composite type can be rigidly described by a structural type called a
"_shape_".

## Shape Keys

A shape within the PHP language can be applied to any array or object
and contain either **implicit** or **explicit** keys.

<compare first-title="Explicit Keys" second-title="Implicit Keys">

```php
array{
    a: First,
    42: Second,
    OTHER_*: Third,
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

> The use of **explicit** and **implicit** keys in one shape is **not allowed**
{style="warning"}


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
[identifier](basic-types.md), the same as used for a type name.

> ```php
> array{
>     a: First,
>     b: Second,
> }
> ```


### Numeric Keys

An explicit key may also be an [integer literal](literal-types.md#integer).

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

### Constant Mask Keys

A field key may also be a [class constant mask or global constant
mask](const-types.md#constant-masks), referring to a whole set of constants
sharing a common prefix.

<tabs>
<tab title="Examples">

> A [class constant mask](const-types.md#constant-masks) as a field key.
> ```php
> array{
>     Path\To\ClassName::PREFIX_*: string,
> }
> ```

> A [global constant mask](const-types.md#constant-masks) as a field key.
> ```php
> array{
>     JSON_*: string,
> }
> ```

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


## Attributes

<secondary-label ref="tl1.1"/>

Each shape field allows you to define list of additional attributes.
An attribute is additional metadata for a field.

<tabs>
<tab title="Examples">

> Simple attribute with one argument for each shape field.
> ```typescript
> App\Domain\User{
>     #[name("user_name")]
>     userName: non-empty-string,
>     #[skip_when_empty]
>     friends: list<App\Domain\User>,
>     ...
> }
> ```

> Multiple attributes in one group.
> ```typescript
> App\Domain\User{
>     #[complexity(100), skip_when_empty]
>     friends: list<App\Domain\User>,
> }
> ```

> Multiple attribute groups.
> ```typescript
> array{
>     #[serialize("onSerialize")]
>     #[deserialize("onDeserialize")]
>     test?: App\Domain\User,
> }
> ```

</tab>
<tab title="Counterexamples">

> Only valid identifiers are allowed.
> ```typescript
> Collection{
>     #[42]
>     test?: App\Domain\User,
> } 
> ```
> 
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "42"
> ```
> {style="warning"}

</tab>
</tabs>
