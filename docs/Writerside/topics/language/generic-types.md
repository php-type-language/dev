# Generic Types

<show-structure for="chapter" depth="2"/>

Each type can take arguments containing other type declarations. Validation of
arguments and their number lies on the implementing side; the parser does not
limit their number or nesting and does not check their semantic correctness in
any way.

Each argument is enclosed in triangle brackets `<` and `>` and separated by a
comma (`,`).

<note>

Please note that throughout the documentation the term "**template argument**"
is used because the similar term "**template parameter**" is used to describe
a declaration.

If the PHP language supported generic types, then the declaration
(in pseudo-language) of the type:

* `interface Traversable<node of array-key, TValue of mixed>` would contain
  **parameters**

and the reference to the type:

* `Traversable<int<0, max>, string>` would contain **arguments**.

</note>

<tabs>
<tab title="Examples">

> Reference to an existing class, interface or enum with template arguments.
> ```typescript
> Path\To\ExampleClass<T, U>
> ```

> Reference to builtin type with template arguments containing other generics.
>  ```typescript
>  iterable<int<0, max>, Collection<User>>
>  ```

> Trailing comma is allowed.
>  ```typescript
>  HashMap<Request, User,>
>  ```

</tab>
<tab title="Counterexamples">

> Missing template argument.
> ```typescript
> example<>
> ```
>
> An error similar to the one below should occur
> ```
> Error: A template argument is expected after "<"
> ```
> {style="warning"}

> Leading comma is NOT allowed.
> ```typescript
> example<,T>
> ```
>
> An error similar to the one below should occur
> ```
> Error: A template argument is expected after "<"
> ```
> {style="warning"}

</tab>
</tabs>

## Call-Site Hints


Each template argument allows you to define an additional hint, which can be 
used, for example, in static analyzers to indicate the
[call-site variance](https://phpstan.org/blog/guide-to-call-site-generic-variance#call-site-variance).

The hint is one of the words `covariant`, `contravariant` or `invariant`
written in lower case before the type of the argument.

> One template argument can contain only one hint.
> {style="warning"}

> The hint words are ordinary names outside of this place: `Foo<covariant>` is
> a reference to the type named `covariant`.
> {style="note"}

<tabs>
<tab title="Examples">

> The `covariant` hint before the template argument`s type.
> ```typescript
> HashMap<array-key, covariant Request>
> ```

> Hints of different arguments are independent of each other.
> ```typescript
> Handler<contravariant Request, invariant Response>
> ```

> The hint applies to the whole type that follows it.
> ```typescript
> Collection<covariant Foo|Bar>
> ```

</tab>
<tab title="Counterexamples">

> Only the three hint words are allowed.
> ```typescript
> Collection<out User>
> ```
> 
> An error similar to the one below should occur
> ```
> Error: Invalid template arguments, "," or ">" is expected
> ```
> {style="warning"}

> Multiple hints are not allowed.
> ```typescript
> HashMap<array-key, covariant covariant Request>
> ```
>
> An error similar to the one below should occur
> ```
> Error: Invalid template arguments, "," or ">" is expected
> ```
> {style="warning"}

</tab>
</tabs>


## Wildcard Arguments

A template argument may be left unsaid, written as an asterisk (`*`) in the
place of the type. A wildcard argument does not accept a [hint](#call-site-hints).

<tabs>
<tab title="Examples">

> Any argument at all.
> ```typescript
> Collection<*>
> ```

> A wildcard beside a type.
> ```typescript
> HashMap<array-key, *>
> ```

> Several wildcards.
> ```typescript
> HashMap<*, *>
> ```

</tab>
<tab title="Counterexamples">

> An asterisk is a template argument alone and is not a type of its own.
> ```typescript
> int|*
> ```
>
> An error similar to the one below should occur
> ```
> Error: Unexpected "*", expected a type name, an
> identifier, "true", "false" and other
> ```
> {style="warning"}

> A wildcard cannot carry a hint.
> ```typescript
> Collection<covariant *>
> ```
>
> An error similar to the one below should occur
> ```
> Error: Invalid template arguments, "," or ">" is expected
> ```
> {style="warning"}

</tab>
</tabs>


## List Syntax

In addition to modern list declarations such as `list<int>` or
`array<array-key, int>`, the legacy `int[]` syntax is allowed.

<tabs>
<tab title="Examples">

> List (array) of `User` objects.
> ```typescript
> User[]
> ```

> List of list (nested array) of `User` objects.
> ```typescript
> User[][]
> ```

</tab>
<tab title="Counterexamples">

> Square brackets with a type inside are not a list, but an
> [offset access](offset-access.md): `User[int]` is the type of the element of
> `User` at the `int` key (was [used in the PSR](https://github.com/php-fig/event-dispatcher/blob/1.0.0/src/ListenerProviderInterface.php#L14) by mistake as a list).
> ```typescript
> User[int]
> ```
> {style="warning"}

</tab>
</tabs>
