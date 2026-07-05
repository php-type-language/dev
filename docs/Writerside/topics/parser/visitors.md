# Visitors

<primary-label ref="parser-component"/>
<link-summary>
Traverse, search, and rewrite a parsed AST using `TypeLang\Parser\Traverser`
and the visitor implementations shipped with the package.
</link-summary>
<show-structure for="chapter" depth="2"/>

To walk the whole graph of an already-parsed type, use the
`TypeLang\Parser\Traverser` class. It accepts one or more
`TypeLang\Parser\Traverser\VisitorInterface` implementations and calls each
of them for every node it visits.

Let's write a visitor that prints the class name of every node it enters,
using the convenience `TypeLang\Parser\Traverser\Visitor` abstract class
(which no-ops every lifecycle method, so you only override what you need):

```php
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\Command;
use TypeLang\Parser\Traverser\Visitor;
use TypeLang\Type\Node;

$type = new TypeLang\Parser\TypeParser()
    ->parse('array<array-key, object{ key?: int<0, max> }>');

$visitor = new class extends Visitor {
    public function enter(Node $node): ?Command
    {
        echo \sprintf(" - %s\n", $node::class);

        return null;
    }
};

Traverser::new([$visitor])
    ->traverse([$type]);
```

```
 - TypeLang\Type\NamedTypeNode
 - TypeLang\Type\Name
 - TypeLang\Type\Identifier
 - TypeLang\Type\Template\TemplateArgumentListNode
 - TypeLang\Type\Template\TemplateArgumentNode
 - TypeLang\Type\NamedTypeNode
 - TypeLang\Type\Name
 - TypeLang\Type\Identifier
 - TypeLang\Type\Template\TemplateArgumentNode
 - TypeLang\Type\NamedTypeNode
 - TypeLang\Type\Name
 - TypeLang\Type\Identifier
 - TypeLang\Type\Shape\FieldsListNode
 - TypeLang\Type\Shape\NamedFieldNode
 - TypeLang\Type\Identifier
 - TypeLang\Type\NamedTypeNode
 - TypeLang\Type\Name
 - TypeLang\Type\Identifier
 - TypeLang\Type\Template\TemplateArgumentListNode
 - TypeLang\Type\Template\TemplateArgumentNode
 - TypeLang\Type\Literal\IntLiteralNode
 - TypeLang\Type\Template\TemplateArgumentNode
 - TypeLang\Type\NamedTypeNode
 - TypeLang\Type\Name
 - TypeLang\Type\Identifier
```
{collapsible="true" collapsed-title="Output"}

## The Visitor Lifecycle

`VisitorInterface` (and its `Visitor` no-op base class) has four methods,
called in this order for a single `traverse()` call:

| Method                        | Called                                                |
|-------------------------------|-------------------------------------------------------|
| `before(): void`              | once, before the first node is visited                |
| `enter(Node $node): ?Command` | once per node, on the way down, before its children   |
| `leave(Node $node): void`     | once per node, on the way back up, after its children |
| `after(): void`               | once, after the last node has been visited            |

```
before
enter UnionTypeNode
  enter NamedTypeNode(int)
  leave NamedTypeNode(int)
  enter NamedTypeNode(string)
  leave NamedTypeNode(string)
leave UnionTypeNode
after
```

`enter()` may return `TypeLang\Parser\Traverser\Command::SkipChildren` to
prevent the traverser from descending into that node's children — `leave()`
is still called for the node itself, just not for anything beneath it.

```php
use TypeLang\Type\Template\TemplateArgumentNode;

$visitor =  new class extends Visitor {
    public function enter(Node $node): ?Command
    {
        if ($node instanceof TemplateArgumentNode) {
            return Command::SkipChildren;
        }

        echo \sprintf(" - %s\n", $node::class);

        return null;
    }
};

Traverser::new([$visitor])
    ->traverse([$type]);
```

## Traverser API

* `new Traverser(iterable $visitors = [])` — construct directly from a list
  of visitors, or use the equivalent static `Traverser::new($visitors)`.
* `Traverser::through(VisitorInterface $visitor, iterable $nodes)` — shortcut
  for the common "one visitor, traverse once, give it back to me" pattern —
  wraps the visitor in a new traverser, runs it, and returns the same
  visitor instance so you can immediately read state off it:
  ```php
  // Find & collect all "Name" nodes
  $find = Traverser::through(new ClassNameMatcherVisitor(Name::class), [
    $type
  ]);
  ```
* `->with(VisitorInterface $visitor, bool $prepend = false)` — returns a new
  traverser with an additional visitor appended (or prepended).
* `->withPropertyAccessor(PropertyAccessorInterface $propertyAccessor)` —
  returns a new traverser that discovers a node's children through a custom
  `PropertyAccessorInterface` instead of the default reflection-based one —
  see below.

## Custom Property Access

By default, the traverser discovers a node's children via
`Traverser\PropertyAccessor\SimplePropertyAccessor`, which reflects over
every non-static, non-hooked public and non-public property and descends
into whichever ones hold a `Node` or an iterable of them. You can supply
your own `PropertyAccessorInterface` implementation (e.g. to only look at a
specific subset of properties, for performance or for a custom node
hierarchy) via `Traverser::withPropertyAccessor()`.

## Built-in Visitors

<deflist>
<def title="MatcherVisitor / ClassNameMatcherVisitor">

Find the first node matching an arbitrary predicate, or matching a given
class. See [](matcher-visitor.md) and [](class-name-matcher-visitor.md).

</def>
<def title="DumperVisitor / StreamDumperVisitor / StringDumperVisitor">

Render the whole tree as an indented, human-readable dump — to a stream or
into a string. See [](dumper-visitor.md), [](stream-dumper-visitor.md),
and [](string-dumper-visitor.md).

</def>
<def title="TypeMapVisitor">

Rewrite every `Name` occurrence in a tree via a callback — the mechanism
behind [Name resolution](type-resolver.md). See [](type-map-visitor.md).

</def>
</deflist>
