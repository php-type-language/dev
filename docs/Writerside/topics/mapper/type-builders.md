# Type Builders

The Type Builder is a factory class that acts as a layer between the
[type declaration](basic-types.md) (like `non-empty-string`) and the
[type instance](types.md) (like `NonEmptyStringType`).

To register types in the `Mapper`, a [platform is used](mapper-platforms.md)
containing a set of type builders that analyze the type description (and AST)
and create a set of [type instances](types.md) used directly at runtime for
checking and casting values.

To better understand the tasks of the builder, it makes sense to look at the 
graph illustrating the process of compiling a type when calling 
`$mapper->denormalize(42)` that should return `int(42)` using built-in 
`TypeInterface<int>` type

<code-block lang="mermaid" xmlns="">
<![CDATA[
flowchart TD
    Input["int(42)"] .-> Mapper["Mapper::denormalize(42)"]
    Mapper -. "cast value 42 using TypeInterface" .-> Type
    Mapper -- "start compilation" --> Extractor["TypeExtractor::extract(42)"]
    Extractor -- "for int(42) value the string(&quot;int&quot;) was inferred" --> Parser["TypeParser::parse(&quot;int&quot;)"]
    Parser -- "definition string(&quot;int&quot;) was parsed to AST object(TypeStatement)" --> Repository["TypeRepository::findType(TypeStatement)"]
    Platform -- "configure builders list&lt;TypeBuilderInterface>" --> Repository
    Repository -. TypeInterface&lt;int> .-> Type["TypeInterface&lt;int>::cast(42)"]
    Type .-> Output["int(42)"]
]]>
</code-block>

The diagram is quite complex, and it's okay if you don't quite understand
the process. The main thing is that it makes sense:
- [The platform](mapper-platforms.md) contains a set of type builders and
  [registers](standard-platform.md#additional-types) them in the repository.
- The type builder returns a [specific `TypeInterface<T>`](types.md) from the
  type declaration string (or more precisely, from its Abstract Syntax Tree).


That is, the code of type builder of `int<0, 10>` looks something like this:

```php
class IntTypeBuilder interface TypeBuilderInterface
{
    public function build(TypeStatement $stmt): TypeInterface
    {
        // [[[Expects named type|basic-types.md]]]
        assert($stmt instanceof NamedTypeNode);
        // [[[Expects 2 template arguments|generic-types.md]]]
        assert(count($stmt->arguments) === 2);
        // [[[Expects no shape fields|shape-types.md]]]
        assert($stmt->fields === null);

        return new IntType(
            name: $stmt->name->toString(), // string("int");
            min: $stmt->arguments[0]->getValue(), // int(0)
            max: $stmt->arguments[1]->getValue(), // int(10)
        );
    }
}
```

<warning>
This is not an exact declaration and is used as an example.

It's also worth noting that not all checks are performed in the 
example. For example, the type of the template arguments (that they are 
integers) also makes sense to check.
</warning>

<tip>
You can read more about custom type builders in the 
<a href="custom-type-builder.md">"custom type builders" page</a>.
</tip>