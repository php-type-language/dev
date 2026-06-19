# Tolerant Mode

<primary-label ref="parser-component"/>

The "tolerant" analysis mode allows parsing a type grammar containing other
arbitrary data.

Such a mode will be convenient to use, for example, for analyzing phpdoc
docblocks, separating types from their descriptions.

```php
$parser = new TypeLang\Parser\Parser(
    // enable "tolerant" mode
    tolerant: true,
);
```

As an example, let's try to parse the contents of the "`@return`" docblock.
Similar actions can be implemented for other annotations.

```php
$parser = new TypeLang\Parser\Parser(
    tolerant: true,
);

$content = <<<'PHP'
    @return Example<T> Returns something very interesting!
    PHP;

// There is no need to analyze the "@return" annotation,
// we only get the content:
//   "Example<T> Returns something very interesting!"
$content = substr($content, strlen('@return '));

var_dump($parser->parse($content));
```

```php
TypeLang\Parser\Node\Stmt\NamedTypeNode {
  +offset: 0
  +name: TypeLang\Parser\Node\Name {
    +offset: 0
    -parts: array:1 [
      0 => TypeLang\Parser\Node\Identifier {
        +offset: 0
        +value: "Example"
      }
    ]
  }
  +arguments: TypeLang\Parser\Node\Stmt\Template\ArgumentsListNode {
    +offset: 7
    +items: array:1 [
      0 => TypeLang\Parser\Node\Stmt\Template\ArgumentNode {
        +offset: 8
        +hint: null
        +value: TypeLang\Parser\Node\Stmt\NamedTypeNode {
          +offset: 8
          +name: TypeLang\Parser\Node\Name {
            +offset: 8
            -parts: array:1 [
              0 => TypeLang\Parser\Node\Identifier {
                +offset: 8
                +value: "T"
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


The `Parser::$lastProcessedTokenOffset` contains the offset in bytes
(in this case 11) at which unparsable content begins (description
of "`@return`" annotation).

```php
// Next we can get the contents of the docblock description by simply
// getting it by this offset.
$description = substr($content, $parser->lastProcessedTokenOffset);
```

The `$description` will contain:

```
string("Returns something very interesting!")
```
