# Type Offsets

<show-structure for="chapter" depth="2"/>
<secondary-label ref="tl1.4"/>

To specify a reference to a specific <a href="shape-types.md">shape or array element</a>, 
the offset access syntax is used.

A description of this type consists of two parts: Arbitrary type description; 
The type of specific element, enclosed in square `[]` brackets.

<tabs>
<tab title="Examples">

> Type with string key "offset".
> ```typescript
> T['offset']
> ```

> Type with dependent key.
> ```typescript
> T[U]
> ```

> <a href="shape-types.md">Shape type</a> with numeric key.
> ```typescript
> array{int, string}[0]
> ```

> Complex example using <a href="shape-types.md">shape types</a> 
> and <a href="generic-types.md">generics</a>.
> ```typescript
> T<U>[object{key: int, ...}]
> ```

</tab>
<tab title="Counterexamples">

> The offset is enclosed in single brackets.
> ```typescript
> Collection[[Some]]
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "["
> ```
> {style="warning"}

> There must be a type description first, followed 
> by a reference to an element of that type.
> ```typescript
> Collection['key']{key: string}
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "{"
> ```
> {style="warning"}

</tab>
</tabs>

<note>
Note that since this is a type description language, any key type is allowed, 
including objects, <a href="conditional-types.md">conditions</a> or 
<a href="shape-types.md">shapes</a>.
</note>
