# ClassNameMatcherVisitor

<primary-label ref="parser-component"/>
<link-summary>
Find the first node in a tree that is an instance of a given class.
</link-summary>
<show-structure for="chapter" depth="2"/>

To search for the first node matching a given class, use
`ClassNameMatcherVisitor` — a thin [`MatcherVisitor`](matcher-visitor.md)
specialization that matches by `instanceof`.

```php
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\ClassNameMatcherVisitor;
use TypeLang\Type\Name;

$type = new TypeLang\Parser\TypeParser()
    ->parse('array<array-key, mixed>');

$finder = Traverser::through(
    visitor: new ClassNameMatcherVisitor(Name::class), 
    nodes: [$type],
);

var_dump($finder->isFound); // bool(true)
var_dump($finder->node);
```

```php
object(TypeLang\Type\Name)#1 (3) {
  ["offset"]=> int(0)
  ["segments"]=> array(1) {
    [0]=> object(TypeLang\Type\Identifier)#2 (2) {
      ["offset"]=> int(0)
      ["value"]=> string(5) "array"
    }
  }
  ["isFullyQualified"]=> bool(false)
}
```
{collapsible="true" collapsed-title="Result"}

Since it finds the *first* match in traversal order, searching for a more
specific class than the root node's own returns the first nested occurrence.
Here, looking for any `NamedTypeNode` inside a union returns the left
operand:

```php
use TypeLang\Type\NamedTypeNode;

$type = new TypeLang\Parser\TypeParser()
    ->parse('int|string');

$finder = Traverser::through(
    visitor: new ClassNameMatcherVisitor(NamedTypeNode::class), 
    nodes: [$type],
);

// string(3) "int"
var_dump($finder->node->name->toString());
```

If nothing matches, `$finder->node` stays `null` and `$finder->isFound` 
is `false`:

```php
use TypeLang\Type\Literal\BoolLiteralNode;

$finder = Traverser::through(
    visitor: new ClassNameMatcherVisitor(BoolLiteralNode::class), 
    nodes: [$type],
);

var_dump($finder->isFound); // bool(false)
var_dump($finder->node);   // NULL
```
