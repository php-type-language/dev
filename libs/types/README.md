<a href="https://github.com/php-type-language" target="_blank">
    <img align="center" src="https://github.com/php-type-language/.github/blob/master/assets/dark.png?raw=true">
</a>

<p align="center">
    <a href="https://packagist.org/packages/type-lang/types"><img src="https://poser.pugx.org/type-lang/types/require/php?style=for-the-badge" alt="PHP 8.1+"></a>
    <a href="https://packagist.org/packages/type-lang/types"><img src="https://poser.pugx.org/type-lang/types/version?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/type-lang/types"><img src="https://poser.pugx.org/type-lang/types/v/unstable?style=for-the-badge" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/php-type-language/types/blob/master/LICENSE"><img src="https://poser.pugx.org/type-lang/types/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/php-type-language/types/actions"><img src="https://github.com/php-type-language/types/workflows/tests/badge.svg"></a>
</p>

---

# TypeLang DTO Types

AST node classes for the TypeLang type system.

**TypeLang** is a declarative type language inspired by static analyzers
like [PHPStan](https://phpstan.org/) and [Psalm](https://psalm.dev/docs/).

Read [documentation pages](https://typelang.dev) for more information.

## Installation

The package is available as a Composer repository and can be installed
using the following command in a root of your project:

```sh
composer require type-lang/types
```

## Requirements

- PHP 8.4+

## Node Overview

All nodes extend the abstract `Node` class and expose a single common property:

```php
// Byte offset of the token in the original source string.
public int $offset = 0;
```

### `Identifier`

A single name segment such as `string`, `MyClass`, or `non-empty-string`.
Virtual identifiers (containing `-`) are common in PHPStan/Psalm type aliases.

```php
$id = Identifier::createFromString('  example  '); // trims whitespace

$id->value;      // 'example'
$id->isVirtual;  // false
$id->isBuiltin;  // false
$id->isSpecial;  // false

//
// Virtual vs. Builtin vs. Special
//

// e.g. "array-key", "positive-int", etc.
$virtual = new Identifier('non-empty-string'); 
$virtual->isVirtual;  // true (contains "-")
$virtual->isBuiltin;  // false
$virtual->isSpecial;  // false

// e.g. "float", "bool", "null", "true", etc.
$builtin = new Identifier('int');
$builtin->isVirtual;  // false
$builtin->isBuiltin;  // true
$builtin->isSpecial;  // false

// e.g. "self", "parent", etc.
$special = new Identifier('static');
$special->isVirtual;  // false
$special->isBuiltin;  // false
$special->isSpecial;  // true
```

### `Name`

A fully- or partially-qualified name composed of `Identifier` segments.

```php
$name = Name::createFromString('\TypeLang\Parser\Node');

$name->isFullyQualified; // true
$name->isSimple;         // false
$name->first->value;     // 'TypeLang'
$name->last->value;      // 'Node'
$name->toString();       // '\TypeLang\Parser\Node'

$name->slice(1)
    ->toString();        // 'Parser\Node'
    
$name->toUnqualified()
    ->toString();        // 'TypeLang\Parser\Node'
    
$name->mergeWith(Name::createFromString('Node\Sub'))
    ->toString();        // '\TypeLang\Parser\Node\Sub'
```


### Type Nodes

All type nodes extend the abstract `TypeNode` class.

#### `NamedTypeNode`

The most common node — a named type with optional template arguments and shape fields.

```php
// int
new NamedTypeNode(Name::createFromString('int'));

// array<string, int>
new NamedTypeNode(
    name: Name::createFromString('array'),
    arguments: new TemplateArgumentListNode([
        new TemplateArgumentNode(new NamedTypeNode(
            Name::createFromString('string'),
        )),
        new TemplateArgumentNode(new NamedTypeNode(
            Name::createFromString('int'),
        )),
    ]),
);
```

#### `NullableTypeNode`

Wraps a type to make it nullable (`?Type`).

```php
new NullableTypeNode(new NamedTypeNode(Name::createFromString('string')));
```

#### `UnionTypeNode` / `IntersectionTypeNode`

Represent `A|B|C` and `A&B&C` respectively. Nested unions (or intersections)
of the same kind are automatically flattened.

```php
$union = new UnionTypeNode(
    new NamedTypeNode(Name::createFromString('int')),
    new NamedTypeNode(Name::createFromString('string')),
    new NamedTypeNode(Name::createFromString('null')),
);

$intersection = new IntersectionTypeNode(
    new NamedTypeNode(Name::createFromString('int')),
    new NamedTypeNode(Name::createFromString('string')),
    new NamedTypeNode(Name::createFromString('null')),
);
```

#### `TypesListNode`

Represents the array-shorthand `Type[]`.

```php
// int[]
new TypesListNode(new NamedTypeNode(Name::createFromString('int')));
```

#### `TypeOffsetAccessNode`

Represents an indexed access type `T[K]`.

```php
new TypeOffsetAccessNode(
    type:   new NamedTypeNode(Name::createFromString('T')),
    access: new NamedTypeNode(Name::createFromString('K')),
);
```

#### `CallableTypeNode`

Represents a callable signature.

```php
// callable(int, string): bool
new CallableTypeNode(
    name: Name::createFromString('callable'),
    parameters: new CallableParameterListNode([
        new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('int')),
        ),
        new CallableParameterNode(
            type: new NamedTypeNode(Name::createFromString('string')),
        ),
    ]),
    type: new NamedTypeNode(Name::createFromString('bool')),
);
```

#### `TernaryExpressionNode`

Represents a conditional type expression `subject is Target ? Then : Else`.

```php
new TernaryExpressionNode(
    condition: new EqualConditionNode(
        subject: new NamedTypeNode(Name::createFromString('T')),
        target:  new NamedTypeNode(Name::createFromString('string')),
    ),
    then: new NamedTypeNode(Name::createFromString('non-empty-string')),
    else: new NamedTypeNode(Name::createFromString('T')),
);
```

#### `ConstMaskNode` / `ClassConstNode` / `ClassConstMaskNode`

Represent constant references and wildcard masks.

```php
// Status::ACTIVE
new ClassConstNode(
    class:    Name::createFromString('Status'),
    constant: new Identifier('ACTIVE'),
);

// Status::*
new ClassConstMaskNode(class: Name::createFromString('Status'));

// Foo\Bar\*
new ConstMaskNode(name: Name::createFromString('Foo\Bar'));
```

### Literal Nodes

All literals extend `LiteralNode` and expose `$value` (native PHP type)
and `$raw` (original source token). Most support a static `parse()` factory.

```php
// value: true,  raw: 'true'
BoolLiteralNode::parse('true');
// value: false, raw: 'False'
BoolLiteralNode::parse('False');

// value: 255,   raw: '0xFF', decimal: '255'
IntLiteralNode::parse('0xFF');
// value: 10,    raw: '0b1010'
IntLiteralNode::parse('0b1010');
// value: 1000,  raw: '1_000'
IntLiteralNode::parse('1_000');

// value: 150.0, raw: '1.5e2'
FloatLiteralNode::parse('1.5e2');
 
// value: null,  raw: 'Null'
new NullLiteralNode('Null');

// decodes escape sequences
StringLiteralNode::parse('"hello\nworld"');
// no escape decoding
StringLiteralNode::parse("'raw'");

// value: 'name' (no $), raw: '$name'
VariableLiteralNode::parse('$name');
```

### Condition Nodes

Used as the `$condition` of `TernaryExpressionNode`. All extend `Condition`
and hold `public TypeNode $subject` and `public TypeNode $target`.

| Class                             | Meaning                 |
|-----------------------------------|-------------------------|
| `EqualConditionNode`              | `subject is target`     |
| `NotEqualConditionNode`           | `subject is not target` |
| `GreaterThanConditionNode`        | `subject > target`      |
| `GreaterThanOrEqualConditionNode` | `subject >= target`     |
| `LessThanConditionNode`           | `subject < target`      |
| `LessThanOrEqualConditionNode`    | `subject <= target`     |


### Shape Nodes

Shape fields describe the entries of a structured array type.

```
array{key: string, 0: int, 'literal': bool, ...}
```

| Class                     | Key type             | Example                     |
|---------------------------|----------------------|-----------------------------|
| `ImplicitFieldNode`       | none (positional)    | `array{string}`             |
| `NamedFieldNode`          | `Identifier`         | `array{key: string}`        |
| `StringNamedFieldNode`    | `StringLiteralNode`  | `array{'key': string}`      |
| `NumericFieldNode`        | `IntLiteralNode`     | `array{0: string}`          |
| `ClassConstFieldNode`     | `ClassConstNode`     | `array{Foo::BAR: string}`   |
| `ClassConstMaskFieldNode` | `ClassConstMaskNode` | `array{Foo::BAR_*: string}` |
| `ConstMaskFieldNode`      | `ConstMaskNode`      | `array{Foo\*: string}`      |

All field nodes inherit `public TypeNode $type`, `public bool $isOptional`, and
`public ?AttributeGroupListNode $attributes` from `FieldNode`. Explicit fields
also expose a string `$index` property for the key's string representation.

`FieldsListNode` collects the fields and marks the shape as sealed or unsealed
(`$sealed = true` means no extra keys allowed; `...` in source makes it unsealed).


### Template Argument Nodes

```php
// array<covariant T, int>
new TemplateArgumentListNode([
    new TemplateArgumentNode(
        value: new NamedTypeNode(Name::createFromString('T')),
        hint:  new Identifier('covariant'),
    ),
    new TemplateArgumentNode(
        value: new NamedTypeNode(Name::createFromString('int')),
    ),
]);
```


### Callable Parameter Nodes

```php
// callable(int $a, string ...$b): void
new CallableParameterListNode([
    new CallableParameterNode(
        type: new NamedTypeNode(Name::createFromString('int')),
        name: VariableLiteralNode::parse('$a'),
    ),
    new CallableParameterNode(
        type:       new NamedTypeNode(Name::createFromString('string')),
        name:       VariableLiteralNode::parse('$b'),
        isVariadic: true,
    ),
]);
```

Constraints enforced by `CallableParameterNode`:
- At least one of `$type` or `$name` must be provided.
- `$isVariadic` and `$isOptional` cannot both be `true`.


### Attribute Nodes

Represent PHP-attribute-style annotations that some type systems attach to type positions.

```
#[Pure, Deprecated('Use X instead')]
```

```
AttributeGroupListNode          (#[X] #[Y])
└── AttributeGroupNode          (#[A, B, C])
    └── AttributeNode           (Pure, Deprecated)
        └── AttributeArgumentListNode
            └── AttributeArgumentNode  (value: StringLiteralNode 'Use X instead')
```


### Node Lists

All list containers extend `NodeList` and implement `IteratorAggregate`,
`ArrayAccess`, and `Countable`.

```php
$list = new TemplateArgumentListNode([...]);

count($list);          // number of items
$list[0];              // first item
$list->first;          // first item
$list->last;           // last item
$index = $list->findIndex($node); // position by identity, or null
foreach ($list as $item) { ... }
```
