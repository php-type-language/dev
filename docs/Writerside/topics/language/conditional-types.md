# Conditional Types

<show-structure for="chapter" depth="2"/>

A conditional (ternary) type describes a type that depends on the result of
comparing a **subject** with some other type. It consists of a condition,
followed by a question mark (`?`), the type used **if the condition is true**,
a colon (`:`), and the type used **if the condition is false**.

```
<Subject> <Operator> <Type> ? <TypeIfTrue> : <TypeIfFalse>
```

> ```typescript
> T is int ? "int type" : "not int type"
> ```

Both the **subject** and the type being compared against can be a [union](logical-types.md#union-types)
(and so any type below it: an intersection, a nullable, a list, etc.) or a
variable (`$name`). The branches can be any type, including another
conditional type, so the chain `T is A ? B : C is D ? E : F` is read as
`T is A ? B : (C is D ? E : F)`.

> A variable is not a type of its own, so a variable subject always requires
> the `is` / `is not` operator behind it.
> {style="note"}

<tabs>
<tab title="Examples">

> A named type as the subject.
> ```typescript
> T is int ? A : B
> ```

> A variable as the subject.
> ```typescript
> $value is int ? A : B
> ```

> A [union type](logical-types.md#union-types) as the subject.
> ```typescript
> T is A|B ? C : D
> ```

> A [callable type](callable-types.md) as the subject.
> ```typescript
> foo() is int ? A : B
> ```

> Parentheses may be used to disambiguate or nest conditional types.
> ```typescript
> T is int ? (U is string ? A : B) : C
> ```

</tab>
<tab title="Counterexamples">

> A bare question mark, without a preceding condition, is not a conditional
> type: it will be parsed as the beginning of a [nullable type](logical-types.md#nullable-types)
> and fail because the placement of `?` is wrong.
> ```typescript
> T ? U : V
> ```
>
> An error similar to the one below should occur
> ```
> Error: Unexpected "?"
> ```
> {style="warning"}

</tab>
</tabs>

## Equality Operators

The `is` keyword checks whether the subject **is** of the compared type, and
`is not` checks the opposite. Both keywords are written in lower case only:
`IS` is an ordinary name, not a keyword. Whitespace and comments are allowed
between the words of `is not`.

<tabs>
<tab title="Examples">

> Positive equality check.
> ```typescript
> T is int ? A : B
> ```

> Negative equality check.
> ```typescript
> T is not int ? A : B
> ```

> A comment between the words of `is not`.
> ```typescript
> T is /* comment */ not int ? A : B
> ```

</tab>
<tab title="Counterexamples">

> The PHP-like `==` and `!=` operators are not recognized: use the `is` and
> `is not` keywords instead.
> ```typescript
> T == int ? A : B
> ```
>
> An error similar to the one below should occur
> ```
> Error: Unexpected "="
> ```
> {style="warning"}

> The `else` branch is required.
> ```typescript
> T is int ? A
> ```
>
> An error similar to the one below should occur
> ```
> Error: The condition requires an else branch, ":" expected
> ```
> {style="warning"}

</tab>
</tabs>

## Yoda-Style Conditions

Since the subject and the compared type can both be arbitrary type
expressions, the condition may also be written in
[Yoda-style](https://en.wikipedia.org/wiki/Yoda_conditions), with the concrete
type on the left and the subject (a type or a variable) on the right.

<tabs>
<tab title="Examples">

> Yoda-style equality check with a named type as the subject.
> ```typescript
> int is T ? A : B
> ```

> Yoda-style equality check with a variable as the subject.
> ```typescript
> int is $value ? A : B
> ```

> A variable may also stand on both sides.
> ```typescript
> $value is not $other ? A : B
> ```

</tab>
</tabs>
