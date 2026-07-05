# MatcherVisitor

<primary-label ref="parser-component"/>
<link-summary>
The generic "find the first node matching an arbitrary predicate" visitor
that `ClassNameMatcherVisitor` specializes.
</link-summary>
<show-structure for="chapter" depth="2"/>

`MatcherVisitor` is the generic base behind
[`ClassNameMatcherVisitor`](class-name-matcher-visitor.md). Use it directly
when the condition you're searching for is more than just "is an instance of
this class".

```php
public function __construct(
    \Closure $matcher,
    ?\Closure $break = null,
) {}
```

* `$matcher` — called for every node; the first node for which it returns
  `true` becomes the match, and traversal of that subtree stops there
  (`Command::SkipChildren` is returned automatically once found, and for
  every node visited afterwards).
* `$break` — optional early-exit predicate, checked whenever `$matcher`
  itself didn't match; if it returns `true`, the search stops there (leaving
  `node` as `null`) without visiting the rest of the tree.

After a `traverse()` call, `$matcher->node` holds the matched node (or
`null`), and `$matcher->isFound` is a convenience boolean for the same.

## Example

Find the first named type whose name is longer than a given length:

```php
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\MatcherVisitor;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\Node;

$type = new TypeLang\Parser\TypeParser()
    ->parse('int|non-empty-string|bool');

$finder = Traverser::through(new MatcherVisitor(
    static fn(Node $node): bool => $node instanceof NamedTypeNode
        && \strlen($node->name->toString()) > 5,
), [$type]);

// string(16) "non-empty-string"
var_dump($finder->node->name->toString());
```

## Stopping Early with `$break`

The `$break` callback lets you bail out before scanning the whole tree once
some other condition is met — useful when you know the node you're looking
for can't possibly occur past a certain point:

```php
use TypeLang\Type\Shape\FieldsListNode;
use TypeLang\Type\Literal\BoolLiteralNode;

$finder = Traverser::through(
    new MatcherVisitor(
        matcher: static fn(Node $node): bool 
            => $node instanceof BoolLiteralNode,
        // give up as soon as we step into a shape's field list
        break: static fn(Node $node): bool 
            => $node instanceof FieldsListNode,
    ),
    [$type],
);
```

`ClassNameMatcherVisitor(SomeClass::class)` is exactly
`new MatcherVisitor(static fn(Node $node): bool => $node instanceof SomeClass)`
with no `$break` — reach for it directly whenever an `instanceof` check is
all you need.
