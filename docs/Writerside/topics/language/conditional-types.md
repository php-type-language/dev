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

Both the **subject** and the type being compared against can be an arbitrary
type expression or a [variable](literal-types.md).

<tabs>
<tab title="Examples">

> A named type as the subject.
> ```typescript
> T is int ? A : B
> ```

> A [variable](literal-types.md) as the subject.
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
> ParseException: Syntax error, unexpected "?"
> ```
> {style="warning"}

</tab>
</tabs>

## Equality Operators

The `is` keyword checks whether the subject **is** of the compared type, and
`is not` checks the opposite. Both keywords are case-insensitive.

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
> ParseException: Syntax error, unexpected "="
> ```
> {style="warning"}

</tab>
</tabs>

## Comparison Operators

In addition to the `is` / `is not` equality checks, TypeLang also allows
comparing the subject using the `<`, `>`, `<=` and `>=` operators.

<tabs>
<tab title="Examples">

> Less than.
> ```typescript
> T < int<0, 100> ? A : B
> ```

> Greater than.
> ```typescript
> T > 0 ? A : B
> ```

> Less than or equal.
> ```typescript
> T <= 100 ? A : B
> ```

> Greater than or equal.
> ```typescript
> T >= 0 ? A : B
> ```

</tab>
</tabs>

> This is a TypeLang-specific extension: neither PHPStan nor Psalm support
> comparison operators in conditional types, only `is` and `is not`. See the
> [syntax comparison](comparison.md) page for details.
> {style="note"}

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

> Yoda-style equality check with a [variable](literal-types.md) as the subject.
> ```typescript
> int is $value ? A : B
> ```

</tab>
</tabs>
