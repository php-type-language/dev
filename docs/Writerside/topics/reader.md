# The Reader Component

<primary-label ref="reader-component"/>
<show-structure for="chapter" depth="2"/>

This package provides a set of methods for reading PHP metadata and 
converting it into the TypeLang AST Nodes.

## Installation

<tldr>
    <p>
        Via <a href="https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies">Composer</a>:
        <code lang="bash">composer require type-lang/reader</code>
    </p>
</tldr>

**Requirements:**
* `PHP >= 8.1`

## Usage

Package supports two reader classes.

<deflist>
    <def title="TypeLang\Reader\ReflectionReader">
        Used to read types metadata from reflection objects.
    </def>
    <def title="TypeLang\Reader\AttributeReader">
        Used to read types metadata from attributes.
        <tip>
            Available since <code>type-lang/reader: ^1.1</code>
        </tip>
    </def>
</deflist>

Any reader implements the `TypeLang\Reader\ReaderInterface` interface, which 
contains several methods:

- [`findConstantType(ReflectionClassConstant): ?TypeStatement`](#constants)
- [`findPropertyType(ReflectionProperty): ?TypeStatement`](#properties)
- [`findFunctionType(ReflectionFunctionAbstract): ?TypeStatement`](#functions)
- [`findParameterType(ReflectionParameter): ?TypeStatement`](#parameters)

### Constants

Method `findConstantType()` is used to read types from constants.

<tabs>
<tab title="ReflectionReader">

```php
class Example
{
    public const int EXAMPLE = 0xDEAD_BEEF;
}

$reader = new \TypeLang\Reader\ReflectionReader();

$result = $reader->findConstantType(
    constant: new \ReflectionClassConstant(
        class: Example::class,
        constant: 'EXAMPLE',
    ),
);
```

```php
TypeLang\Parser\Node\Stmt\NamedTypeNode {
  +offset: 0
  +name: TypeLang\Parser\Node\Name {
    +offset: 0
    -parts: array:1 [
      0 => TypeLang\Parser\Node\Identifier {
        +offset: 0
        +value: "int"
      }
    ]
  }
  +arguments: null
  +fields: null
}
```
{collapsible="true" collapsed-title="TypeLang\Parser\Node\Stmt\NamedTypeNode"}

</tab>
<tab title="AttributeReader">

```php
class Example
{
    #[TypeLang\Reader\Attribute\MapType('int<0, max>')]
    public const int EXAMPLE = 0xDEAD_BEEF;
}

$reader = new \TypeLang\Reader\AttributeReader();

$result = $reader->findConstantType(
    constant: new \ReflectionClassConstant(
        class: Example::class,
        constant: 'EXAMPLE',
    ),
);
```

```php
TypeLang\Parser\Node\Stmt\NamedTypeNode {
  +offset: 0
  +name: TypeLang\Parser\Node\Name {
    +offset: 0
    +parts: array:1 [
      0 => TypeLang\Parser\Node\Identifier {
        +offset: 0
        +value: "int"
      }
    ]
  }
  +arguments: TypeLang\Parser\Node\Stmt\Template\TemplateArgumentsListNode {
    +offset: 3
    +items: array:2 [
      0 => TypeLang\Parser\Node\Stmt\Template\TemplateArgumentNode {
        +offset: 4
        +hint: null
        +value: TypeLang\Parser\Node\Literal\IntLiteralNode {#798
          +offset: 4
          +raw: "0"
          +value: 0
        }
        +attributes: null
      }
      1 => TypeLang\Parser\Node\Stmt\Template\TemplateArgumentNode {
        +offset: 7
        +hint: null
        +value: TypeLang\Parser\Node\Stmt\NamedTypeNode {
          +offset: 7
          +name: TypeLang\Parser\Node\Name {
            +offset: 7
            +parts: array:1 [
              0 => TypeLang\Parser\Node\Identifier {
                +offset: 7
                +value: "max"
              }
            ]
          }
          +arguments: null
          +fields: null
        }
        +attributes: null
      }
    ]
  }
  +fields: null
}
```
{collapsible="true" collapsed-title="TypeLang\Parser\Node\Stmt\NamedTypeNode"}

</tab>
</tabs>

### Properties

Method `findPropertyType()` is used to read types from class properties.

<tabs>
<tab title="ReflectionReader">

```php
class Example
{
    public readonly string $test;
}

$reader = new \TypeLang\Reader\ReflectionReader();

$result = $reader->findPropertyType(
    property: new \ReflectionProperty(
        class: Example::class,
        property: 'test',
    ),
);
```

```php
TypeLang\Parser\Node\Stmt\NamedTypeNode {
  +offset: 0
  +name: TypeLang\Parser\Node\Name {
    +offset: 0
    -parts: array:1 [
      0 => TypeLang\Parser\Node\Identifier {
        +offset: 0
        +value: "string"
      }
    ]
  }
  +arguments: null
  +fields: null
}
```
{collapsible="true" collapsed-title="TypeLang\Parser\Node\Stmt\NamedTypeNode"}
</tab>
<tab title="AttributeReader">

```php
class Example
{
    #[TypeLang\Reader\Attribute\MapType('non-empty-string')]
    public readonly string $test;
}

$reader = new \TypeLang\Reader\AttributeReader();

$result = $reader->findPropertyType(
    property: new \ReflectionProperty(
        class: Example::class,
        property: 'test',
    ),
);
```

```php
TypeLang\Parser\Node\Stmt\NamedTypeNode {
  +offset: 0
  +name: TypeLang\Parser\Node\Name {
    +offset: 0
    +parts: array:1 [
      0 => TypeLang\Parser\Node\Identifier {
        +offset: 0
        +value: "non-empty-string"
      }
    ]
  }
  +arguments: null
  +fields: null
}
```
{collapsible="true" collapsed-title="TypeLang\Parser\Node\Stmt\NamedTypeNode"}
</tab>
</tabs>

### Functions

Method `findFunctionType()` is used to read return types from
functions and class methods.

<tabs>
<tab title="ReflectionReader">

```php
$example = function(): void {};

$reader = new \TypeLang\Reader\ReflectionReader();

$result = $reader->findFunctionType(
    function: new \ReflectionFunction($example),
);
```

```php
TypeLang\Parser\Node\Stmt\NamedTypeNode {
  +offset: 0
  +name: TypeLang\Parser\Node\Name {
    +offset: 0
    -parts: array:1 [
      0 => TypeLang\Parser\Node\Identifier {
        +offset: 0
        +value: "void"
      }
    ]
  }
  +arguments: null
  +fields: null
}
```
{collapsible="true" collapsed-title="TypeLang\Parser\Node\Stmt\NamedTypeNode"}

</tab>
<tab title="AttributeReader">

```php
$example = #[TypeLang\Reader\Attribute\MapType('never')]
    function(): void {};

$reader = new \TypeLang\Reader\AttributeReader();

$result = $reader->findFunctionType(
    function: new \ReflectionFunction($example),
);
```

```php
TypeLang\Parser\Node\Stmt\NamedTypeNode {
  +offset: 0
  +name: TypeLang\Parser\Node\Name {
    +offset: 0
    -parts: array:1 [
      0 => TypeLang\Parser\Node\Identifier {
        +offset: 0
        +value: "never"
      }
    ]
  }
  +arguments: null
  +fields: null
}
```
{collapsible="true" collapsed-title="TypeLang\Parser\Node\Stmt\NamedTypeNode"}

</tab>
</tabs>

### Parameters

Method `findParameterType()` is used to read types from
function and method parameters.

<tabs>
<tab title="ReflectionReader">

```php
$example = function(bool $param) {};

$reader = new \TypeLang\Reader\ReflectionReader();

$result = $reader->findParameterType(
    parameter: new \ReflectionParameter(
        function: $example,
        param: 'param',
    ),
);
```

```php
TypeLang\Parser\Node\Stmt\NamedTypeNode {
  +offset: 0
  +name: TypeLang\Parser\Node\Name {
    +offset: 0
    -parts: array:1 [
      0 => TypeLang\Parser\Node\Identifier {
        +offset: 0
        +value: "bool"
      }
    ]
  }
  +arguments: null
  +fields: null
}
```
{collapsible="true" collapsed-title="TypeLang\Parser\Node\Stmt\NamedTypeNode"}

</tab>
<tab title="AttributeReader">

```php
$example = function(
    #[TypeLang\Reader\Attribute\MapType('true')] bool $param,
) {};

$reader = new \TypeLang\Reader\AttributeReader();

$result = $reader->findParameterType(
    parameter: new \ReflectionParameter(
        function: $example,
        param: 'param',
    ),
);
```

```php
TypeLang\Parser\Node\Stmt\NamedTypeNode {
  +offset: 0
  +name: TypeLang\Parser\Node\Name {
    +offset: 0
    -parts: array:1 [
      0 => TypeLang\Parser\Node\Identifier {
        +offset: 0
        +value: "true"
      }
    ]
  }
  +arguments: null
  +fields: null
}
```
{collapsible="true" collapsed-title="TypeLang\Parser\Node\Stmt\NamedTypeNode"}

</tab>
</tabs>

## Attribute Reader

<secondary-label ref="r1.1"/>

The `TypeLang\Reader\AttributeReader` provides the ability to modify its behavior.
To do this, you should pass the `TypeLang\Reader\AttributeReader\AttributeProviderInterface` 
implementation to the constructor of this class.

```php
use TypeLang\Reader\AttributeReader;
use TypeLang\Reader\AttributeReader\AttributeProviderInterface;

$reader = new AttributeReader(
    provider: new class implements AttributeProviderInterface {
        public function getAttribute(): string
        {
            //
            // The class of the attribute to be
            // read should be returned.
            //
            return AttributeClassName::class;
        }

        public function getTypeFromAttribute(
            object $attribute,
        ): string {
            //
            // A value with a type description from
            // the attribute should be returned.
            //
            return $attribute->propertyWithTypeDefinition;
        }

        public function process(
            object $attribute,
            TypeStatement $statement,
        ): TypeStatement {
            //
            // You can also modify the return type based 
            // on information from the attribute.
            //
            return $statement;
        }
    },
);
```


## Complex Example

Complete example for reading and printing types from an entire class.

> The [`type-lang/printer` component](printer.md) is used to print the types.

```php
$class = new \ReflectionClass(Path\To\Example::class);
$printer = new \TypeLang\Printer\PrettyPrinter();
$converter = new \TypeLang\Reader\ReflectionReader();


// Dump all constants with its types.
foreach ($class->getReflectionConstants() as $constant) {
    // Creates type node AST from a constant's type.
    if ($type = $converter->findConstantType($constant)) {
        echo vsprintf("const %s has type %s\n", [
            $constant->name,
            $printer->print($type),
        ]);
    }
}


// Dump all properties with its types.
foreach ($class->getProperties() as $property) {
    // Creates type node AST from a property's type.
    if ($type = $converter->findPropertyType($property)) {
        echo vsprintf("property %s has type %s\n", [
            $property->name,
            $printer->print($type),
        ]);
    }
}


// Dump all methods with its types.
foreach ($class->getMethods() as $method) {
    // Creates type node AST from any function's return type.
    if ($type = $converter->findFunctionType($method)) {
        echo vsprintf("function %s has type %s\n", [
            $method->name,
            $printer->print($type),
        ]);
    }


    // Creates type node AST from a parameter's type.
    foreach ($method->getParameters() as $parameter) {
        if ($type = $converter->findParameterType($parameter)) {
            echo vsprintf("parameter %s has type %s\n", [
                $parameter->name,
                $printer->print($type),
            ]);
        }
    }
}
```