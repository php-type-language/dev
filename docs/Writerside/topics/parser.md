# The Parser Component

<primary-label ref="parser-component"/>
<show-structure for="chapter" depth="2"/>

A parser component is used to analyze and construct types AST with their
information and grammar checking.

## Installation

<tldr>
    <p>
        Via <a href="https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies">Composer</a>:
        <code lang="bash">composer require type-lang/parser</code>
    </p>
</tldr>

**Requirements:**
* `PHP >= 8.1`
* `ext-pcre`
* `ext-mbstring` <sup>optional</sup>

## Usage

To create a parser instance, the `TypeLang\Parser\Parser` class is used.
To run code analysis, you should use the `parse()` method.

```php
$parser = new TypeLang\Parser\Parser();

$result = $parser->parse('example');
```

```php
TypeLang\Parser\Node\Stmt\NamedTypeNode {
  +offset: 0
  +name: TypeLang\Parser\Node\Name {
    +offset: 0
    -parts: array:1 [
      0 => TypeLang\Parser\Node\Identifier {
        +offset: 0
        +value: "example"
      }
    ]
  }
  +arguments: null
  +fields: null
}
```
{collapsible="true" collapsed-title="Result"}

### Feature Toggling

You can enable or disable a set of parser features if your task requires only
partial support of the functionality. Such a feature allows you to conveniently 
implement more strict functionality.

```php
$parser = new TypeLang\Parser\Parser(
    literals: false,
);

$result = $parser->parse('42');

// Uncaught TypeLang\Parser\Exception\ParseException:
//   Literal values not allowed in "42" at column 1
```

### Parser Arguments

The first argument of the `TypeLang\Parser\Parser::parse(<source>)` method corresponds to the source code data
and can be of the following types:

<deflist>
<def title="Method signature">

<tabs>
  <tab title="string">

  ```php
  $result = $parser->parse(<<<'CODE'
      object{ key?: int<0, max> }
  CODE);
  ```
  </tab>
  <tab title="resource (stream)">
  
  ```php
  $result = $parser->parse(
      fopen(__DIR__ . '/source.txt', 'rb'),
  );
  ```
  </tab>
  <tab title="SplFileInfo">

  ```php
  $result = $parser->parse(
      new SplFileInfo(__DIR__ . '/source.txt'),
  );
  ```
  </tab>
  <tab title="ReadableInterface">

  ```php
  $result = $parser->parse(
      Phplrt\Source\File::fromPathname(__DIR__ . '/source.txt'),
  );
  ```
  </tab>
</tabs>
</def>
</deflist>
