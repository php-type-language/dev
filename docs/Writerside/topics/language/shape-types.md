# Shape Types

<show-structure for="chapter" depth="2"/>

Each **composite** type can be rigidly described by a structural type called a
"shape". A shape within the PHP language can be applied to any array or object
and contain either implicit or explicit keys.

<tabs>
<tab title="named explicit keys">

```php
array{
    a: first,
    b: second
}
```
</tab>
<tab title="numeric explicit keys">

```php
array{
    1: first,
    42: second
}
```
</tab>
<tab title="string explicit keys">

```php
array{
    "name-some": first,
    "escape\nchars": second
}
```
</tab>
<tab title="implicit keys">

```php
array{
    first,
    second
}
```
</tab>
</tabs>

> The use of **explicit** and **implicit** keys in one shape is not allowed 
> and causes an exception during parsing.
> ```php
> array{
>     named: first,
>     second
> }
> ```
> 
> An error similar to the one below should occur.
> ```
> ParseException: Cannot mix numeric and named keys.
> ```
{style="warning"}

> Support for other types of **keys**, such as const mask (`Class::CONST_*`)
> is not currently available.
> ```php
> array{
>     Class::CONST_*: string,
>     ...
> }
> ```
> 
> An error similar to the one below should occur.
> ```
> ParseException: Syntax error, unexpected ":"
> ```
{style="warning"}

### Optional Fields

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
    key: Type?,
}
```
</compare>

### Unsealed Shapes

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


### Typed Shapes

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