# Callable Types

<show-structure for="chapter" depth="2"/>

Callable types describe an arbitrary type that describes a function.

Each callable MAY have a list of parameters and/or a return type definition.

> Callable type without parameters and return type.
> ```typescript
> foo()
> ```

> Callable type with 1 parameter with return type.
> ```typescript
> foo(T): void
> ```

> Complex example (see details below).
> ```typescript
> a(int<0, max>, c(?C): mixed): void
> ```

### Return Type

The return type is written after a colon ("`:`"). A union ("`|`"), an
intersection ("`&`") or a condition written behind it is not a part of the
return type, but of the type the callable itself belongs to — the same way
PHPStan, Psalm and Phan read it. To return such a type, group it with
parentheses. A nullable type (`?T`) and a list (`T[]`) are parts of the return
type.

<tabs>
<tab title="Examples">

> Callable type returning a nullable type.
> ```typescript
> foo(): ?A
> ```

> Callable type returning a union.
> ```typescript
> foo(): (A|B)
> ```

> A union of a callable type returning `A` and the `B` type.
> ```typescript
> foo(): A|B
> ```
> 
> Which is the same as
> ```typescript
> (foo(): A)|B
> ```

</tab>
</tabs>


### Named Parameters

The name after the type of the parameter defines the parameter that
[allows passing by name](https://www.php.net/manual/en/functions.arguments.php#functions.named-arguments).

The name must appear after the parameter type and begin with a "`$`" sign.
Just like in the PHP language. The type of a named parameter may be omitted.

A trailing comma after the last parameter is allowed.

<tabs>
<tab title="Examples">

> Callable type with one `$name` named parameter.
> ```typescript
> foo(T $name)
> ```

> Callable type with `$name` named and anonymous parameters.
> ```typescript
> foo(A $a, B, C)
> ``` 

> Callable type with a named parameter without a type.
> ```typescript
> foo($name)
> ```

> Trailing comma.
> ```typescript
> foo(A $a, B $b,)
> ```

</tab>
<tab title="Counterexamples">

> A parameter cannot be empty.
> ```typescript
> foo(A, , B)
> ```
> 
> An error similar to the one below should occur
> ```
> Error: Invalid callable parameters, a parameter, "," or ")" is expected
> ```
> {style="warning"}

</tab>
</tabs>

### Output Parameters

Passing a parameter by reference means that the function can change the passed
variable while it is running.

To indicate that a parameter is passed by reference, an "`&`" sign is used after
the type and before the name. A parameter without a type (only with a name) may
begin with the sign.

<tabs>
<tab title="Examples">

> Callable type with one output (referenced) parameter.
> ```typescript
> foo(T&)
> ```

> Callable type with one output (referenced) named parameter.
> ```typescript
> foo(T &$name)
> ```

> Callable type with one output (referenced) parameter without a type.
> ```typescript
> foo(&$name)
> ```

</tab>
<tab title="Counterexamples">

> The ampersand (`&`) must be placed after the parameter's type.
> ```typescript
> foo(&T)
> ```
>
> An error similar to the one below should occur
> ```
> Error: Invalid callable parameters, a parameter, "," or ")" is expected
> ```
> {style="warning"}

</tab>
</tabs>

### Optional Parameters

An optional parameter means that the argument may not be passed when such a
function is called.

An optional parameter is indicated by the "`=`" sign at the end of the
parameter description.

The sign may be followed by a default value: a [literal](literal-types.md), or a
[constant](const-types.md) (`FOO`, `Foo::BAR`, `Foo::class`). Masks, lists
and other types cannot be a default value. A variadic parameter cannot have
a default value.

<tabs>
<tab title="Examples">

> Callable type with one optional parameter.
> ```typescript
> foo(T=)
> ```

> Callable type with one optional named parameter.
> ```typescript
> foo(T $name=)
> ```

> Callable type with one optional output parameter.
> ```typescript
> foo(T&=)
> ```

> Callable type with one optional output named parameter.
> ```typescript
> foo(T &$name=)
> ```

> Callable type with default values.
> ```typescript
> foo(int $a = 42, string $b = 'text', ?Foo $c = null, int $d = Foo::BAR)
> ```

</tab>
<tab title="Counterexamples">

> The optionality char (`=`) must be placed at the end.
> ```typescript
> foo(T= $name)
> ```
>
> An error similar to the one below should occur
> ```
> Error: Invalid callable parameters, a parameter, "," or ")" is expected
> ```
> {style="warning"}

</tab>
</tabs>

### Variadic Parameters

Variadic parameters are indicated by the "`...`" placed after the type. Where
a parameter carries both markers, the ampersand ("`&`") comes first. The
"`...`" may also stand in front of a type (`...T`), the way it does in
some phpdoc dialects, but then the parameter has no name.

> Variadic parameter cannot be optional since they are already optional.
{style="warning"}

<tabs>
<tab title="Examples">

> Callable type with one variadic parameter.
> ```typescript
> foo(T...)
> ```

> Callable type with one variadic named parameter.
> ```typescript
> foo(T ...$name)
> ```

> Callable type with one variadic output named parameter.
> ```typescript
> foo(T &...$name)
> ```

> Callable type with one variadic parameter without a type.
> ```typescript
> foo(...$name)
> ```

> Callable type with one variadic parameter, the ellipsis stands before the type.
> ```typescript
> foo(...T)
> ```

</tab>
<tab title="Counterexamples">

> The ellipsis placed before the type cannot be followed by a name.
> ```typescript
> foo(...T $name)
> ```
>
> An error similar to the one below should occur
> ```
> Error: Invalid callable parameters, a parameter, "," or ")" is expected
> ```
> {style="warning"}

> The ampersand (`&`) must be placed before the ellipsis (`...`).
> ```typescript
> foo(T ...&$name)
> ```
>
> An error similar to the one below should occur
> ```
> Error: Invalid callable parameters, a parameter, "," or ")" is expected
> ```
> {style="warning"}

> Variadic parameter cannot be optional.
> ```typescript
> foo(T ...$name=)
> ```
> 
> An error similar to the one below should occur
> ```
> Error: Invalid callable parameters, a parameter, "," or ")" is expected
> ```
> {style="warning"}

</tab>
</tabs>

## Template Parameters

A callable MAY declare the template parameters it introduces, written as a
`<...>` list between the name and the parameter list. Each parameter is a
name, optionally followed by the bounds put on it.

A parameter may be prefixed with a variance (`covariant`, `contravariant` or
`invariant`) and accepts three kinds of limit, each written at most once, in
the following order: the upper bound, the lower bound, the default.

* `of T` or `as T` — the **upper bound**: the argument is to be a subtype
  of `T`. The two words mean the same and are kept as they are written.
* `super T` — the **lower bound**: the argument is to be a supertype of `T`.
* `= T` — the **default**: the type the parameter takes when no argument is
  passed. It bounds nothing.

The words are case-sensitive and written in lower case only, an `OF` is an
ordinary name.

> A `<...>` that no parenthesis follows is a list of
> [template arguments](generic-types.md), not of template parameters, and
> template arguments describe no bounds.
> {style="note"}

<tabs>
<tab title="Examples">

> Callable type declaring one template parameter.
> ```typescript
> callable<T>(T): T
> ```

> Callable type declaring a bounded template parameter.
> ```typescript
> callable<T of Some>(T): T
> ```

> Every kind of bound, and several parameters at once.
> ```typescript
> Closure<T of Some, U super Any, V = int>(T, U): V
> ```

> Template parameter with a variance.
> ```typescript
> callable<covariant T>(T): void
> ```

> One parameter carrying every limit at once.
> ```typescript
> callable<T of Some super Any = int>(T): void
> ```

</tab>
<tab title="Counterexamples">

> Bounds belong to a callable alone, so a type used with template arguments
> describes none.
> ```typescript
> Collection<T of Some>
> ```
>
> An error similar to the one below should occur
> ```
> Error: Invalid template arguments, "," or ">" is expected
> ```
> {style="warning"}

> Only `of`, `as` and `super` bound a parameter.
> ```typescript
> callable<T whatever Some>(): void
> ```
>
> An error similar to the one below should occur
> ```
> Error: Invalid template arguments, "," or ">" is expected
> ```
> {style="warning"}

> Each kind of limit is written at most once.
> ```typescript
> callable<T of Some as Any>(): void
> ```
>
> An error similar to the one below should occur
> ```
> Error: Invalid template arguments, "," or ">" is expected
> ```
> {style="warning"}

> The lower bound cannot stand before the upper one.
> ```typescript
> callable<T super Any of Some>(): void
> ```
>
> An error similar to the one below should occur
> ```
> Error: Invalid template arguments, "," or ">" is expected
> ```
> {style="warning"}

> A bound cannot stand behind the default, since it would read as a bound
> of the default itself: the `of Some` below bounds the `int`, not the `T`.
> ```typescript
> callable<T = int of Some>(): void
> ```
>
> An error similar to the one below should occur
> ```
> Error: Invalid template arguments, "," or ">" is expected
> ```
> {style="warning"}

</tab>
</tabs>
