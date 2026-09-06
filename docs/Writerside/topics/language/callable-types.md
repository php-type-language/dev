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


### Named Parameters

The name after the type of the parameter defines the parameter that
[allows passing by name](https://www.php.net/manual/en/functions.arguments.php#functions.named-arguments).

The name must appear after the parameter type and begin with a "`$`" sign.
Just like in the PHP language.

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

</tab>
<tab title="Counterexamples">

> Callable type without parameter's type.
> ```typescript
> foo($name)
> ```
> 
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected ")"
> ```
> {style="warning"}

</tab>
</tabs>

### Output Parameters

Passing a parameter by reference means that the function can change the passed
variable while it is running.

To indicate that a parameter is passed by reference, an "`&`" sign is used after
the type and before the name.

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

</tab>
<tab title="Counterexamples">

> The ampersand (`&`) must be placed after the parameter's type.
> ```typescript
> foo(&T)
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "&"
> ```
> {style="warning"}

</tab>
</tabs>

### Optional Parameters

An optional parameter means that the argument may not be passed when such a
function is called.

An optional parameter is indicated by the "`=`" sign at the end of the
parameter description.

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

</tab>
<tab title="Counterexamples">

> The optionality char (`=`) must be placed at the end.
> ```typescript
> foo(T= $name)
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "$name"
> ```
> {style="warning"}

</tab>
</tabs>

### Variadic Parameters

Variadic parameters are indicated by the "`...`" placed after the type. Where
a parameter carries both markers, the ampersand ("`&`") comes first.

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

</tab>
<tab title="Counterexamples">

> The ellipsis (`...`) must be placed after the parameter's type.
> ```typescript
> foo(...T)
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "..."
> ```
> {style="warning"}

> The ampersand (`&`) must be placed before the ellipsis (`...`).
> ```typescript
> foo(T ...&$name)
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "&"
> ```
> {style="warning"}

> Variadic parameter cannot be optional.
> ```typescript
> foo(T ...$name=)
> ```
> 
> An error similar to the one below should occur
> ```
> ParseException: Cannot have variadic param with a default
> ```
> {style="warning"}

</tab>
</tabs>
