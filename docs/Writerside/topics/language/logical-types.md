# Logical Types

<show-structure for="chapter" depth="2"/>

The TypeLang, [like PHP](https://www.php.net/manual/en/language.types.type-system.php#language.types.type-system.composite), supports two types of composite (logical) types:
Union and Intersection. The ability to specify a _nullable_ type using a 
separate expression is also supported.

### Union Types

Each union type is separated by a pipe character (`|`) and may contain any other
type definition.


> ```typescript
> A | B | C
> ```
> 
> ```mermaid
> graph LR
> U{{ A &vert; B &vert; C }}
> U -- OR --> A(A)
> U -- OR --> B(B)
> U -- OR --> C(C)
> ```

### Intersection Types

Each intersection type is separated by an ampersand character (`&`) and may
contain any other type definition.

> ```typescript
> A & B & C
> ```
> 
> ```mermaid
> graph LR
> U{{ A &amp; B &amp; C }}
> U -- AND --> A(A)
> U -- AND --> B(B)
> U -- AND --> C(C)
> ```

### Nullable Types

Nullable type is a shortened alias for union type `T | null` 
and is written as `?T.`

<tabs>
<tab title="Examples">

> Description of nullable type `Example` (equivalent to `Example | null`)
> ```typescript
> ?Example
> ```
> ```mermaid
> graph LR
> U{{ ?Example }}
> U --> SU{{ Example &vert; null }}
> SU -- OR --> A(Example)
> SU -- OR --> B(null)
> ```

</tab>
<tab title="Counterexamples">

> The question mark must be placed before the type name.
> ```typescript
> Example?
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "?"
> ```
> {style="warning"}
</tab>
</tabs>

### Parentheses

Parentheses can be used to disambiguate types.

> Disjunctive form (DNF).
> 
> ```typescript
> (A & B) | C
> ```
> 
> ```mermaid
> graph LR
> U{{ &lpar; A &amp; B &rpar; &vert; C }}
> U -- OR --> P1{{A &amp; B}}
> P1 -- AND --> A(A)
> P1 -- AND --> B(B)
> U -- OR --> C(C)
> ```


> Conjunctive form (CNF).
> ```typescript
> (A | B) & C
> ```
> 
> ```mermaid
> graph LR
> U{{ &lpar; A &vert; B &rpar; &amp; C }}
> U -- AND --> P1{{A &vert; B}}
> P1 -- OR --> A(A)
> P1 -- OR --> B(B)
> U -- AND --> C(C)
> ```
