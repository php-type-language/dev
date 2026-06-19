# Custom Type Builder

To create a custom type, it may make sense to create a factory (i.e., a type 
builder). This ensures that the [type object](types.md) is created correctly 
and with the required constructor arguments.

The builder object compiles the type based on configuration rules and external 
parameters, so don't be afraid to move all type assembly logic into this class.

For a fully custom implementation of the type builder, one should implement 
the interface `TypeLang\Mapper\Type\Builder\TypeBuilderInterface`, which 
requires the implementation of two methods:
- `isSupported()` - Must return `bool(true)` if the specified type builder can 
  build [the `TypeInterface`](types.md)
- `build()` - Creates [a `TypeInterface` instance](types.md)

```php
use TypeLang\Mapper\Type\Builder\TypeBuilderInterface;
use TypeLang\Mapper\Type\Parser\TypeParserInterface;
use TypeLang\Mapper\Type\Repository\TypeRepositoryInterface;
use TypeLang\Mapper\Type\TypeInterface;
use TypeLang\Parser\Node\Stmt\TypeStatement;

final readonly class CustomTypeBuilder implements TypeBuilderInterface
{
    public function isSupported(TypeStatement $statement): bool
    {
        // TODO: Should match the TypeStatement AST object
    }

    public function build(
        TypeStatement $statement,
        TypeRepositoryInterface $types,
        TypeParserInterface $parser,
    ): TypeInterface {
        // TODO: Should create the TypeInterface instance
    }
}
```

Additionally, don't forget that you can pass additional external
parameters to the type builder's constructor. For example,
a JSON Schema Validator, if you want to create a type that validates
a JSON inside a string, like: `json<"schema.json">`

```php
final readonly class JsonTypeBuilder implements TypeBuilderInterface
{
    public function __construct(
        private JsonSchemaValidator $validator,
    ) {}
}
```

## Matching

First, for the builder to work and construct the type, a checking method
`isSupported()` must be implemented.

For example, if we want to check that the type is called `json`, the code
will look like this:

```php
use TypeLang\Parser\Node\Stmt\NamedTypeNode;

// ...

public function isSupported(TypeStatement $statement): bool
{
    return $statement instanceof NamedTypeNode
        && $statement->name->toLowerString() === 'json';
}
```

Each named type is implemented using
[the `NamedTypeNode` instance](ast-type-statements.md#named-types).

<tip>
A more detailed description of some of the available nodes in 
<a href="ast-type-statements.md">the <code>TypeStatement</code> documentation pages</a>
</tip>


### Building

Next, we need to implement the type creation method. If the type doesn't 
have any external dependencies, then creating the type will be quite simple.

```php
public function build(
    TypeStatement $statement,
    TypeRepositoryInterface $types,
    TypeParserInterface $parser,
): JsonType {
    return new JsonType();
}
```

However, in this case the same `JsonType` instance will be returned, 
**no matter** how you write the type definition:
- `json`
- `json<T>`
- `json{key: val}`
- `json{key: val, ...<T>}`
- etc.

You can add appropriate checks to exclude template arguments and shape fields.

```php
use TypeLang\Mapper\Exception\Definition\{
    Shape\ShapeFieldsNotSupportedException,
    Template\TemplateArgumentsNotSupportedException
};

// ...

public function build(
    TypeStatement $statement,
    TypeRepositoryInterface $types,
    TypeParserInterface $parser,
): JsonType {
    if ($statement->fields !== null) {
        throw ShapeFieldsNotSupportedException
            ::becauseShapeFieldsNotSupported($statement);
    }

    if ($statement->arguments !== null) {
        throw TemplateArgumentsNotSupportedException
            ::becauseTemplateArgumentsNotSupported($statement);
    }

    return new JsonType();
}
```