# Visitors

<primary-label ref="parser-component"/>
<show-structure for="chapter" depth="2"/>

To completely traverse a graph of all types, you can use the
`TypeLang\Parser\Traverser` class. The traverser instance can accept several
the `TypeLang\Parser\Traverser\VisitorInterface` implementation as a constructor
argument.

Let's try to write a simple visitor that will display the names of all classes
when entering each AST node.

```php
use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\Command;
use TypeLang\Parser\Traverser\Visitor;

$traverser = new Traverser([
    new class extends Visitor {
        public function enter(Node $node): ?Command
        {
            echo \sprintf(" - %s\n", $node::class);

            return null;
        }
    }
]);

$traverser->traverse([$result]);
```

You can see the following text as the output result:

```yaml
- TypeLang\Parser\Node\Stmt\NamedTypeNode
- TypeLang\Parser\Node\Name
- TypeLang\Parser\Node\Stmt\Template\ArgumentsListNode
- TypeLang\Parser\Node\Stmt\Template\ArgumentNode
- TypeLang\Parser\Node\Stmt\NamedTypeNode
- TypeLang\Parser\Node\Name
- TypeLang\Parser\Node\Stmt\Template\ArgumentNode
- TypeLang\Parser\Node\Stmt\NamedTypeNode
- TypeLang\Parser\Node\Name
- TypeLang\Parser\Node\Stmt\Shape\FieldsListNode
- TypeLang\Parser\Node\Stmt\Shape\NamedFieldNode
- TypeLang\Parser\Node\Stmt\NamedTypeNode
- TypeLang\Parser\Node\Name
- TypeLang\Parser\Node\Stmt\Template\ArgumentsListNode
- TypeLang\Parser\Node\Stmt\Template\ArgumentNode
- TypeLang\Parser\Node\Literal\IntLiteralNode
- TypeLang\Parser\Node\Stmt\Template\ArgumentNode
- TypeLang\Parser\Node\Stmt\NamedTypeNode
- TypeLang\Parser\Node\Name
- TypeLang\Parser\Node\Identifier
```

## Commands

### Skip Children Nodes

To skip any nodes when traversing, you can use the appropriate
command `TypeLang\Parser\Traverser\Command::SKIP_CHILDREN`.

```php
use TypeLang\Parser\Node\Node;
use TypeLang\Parser\Node\Stmt\Template\TemplateArgumentNode;
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\Command;
use TypeLang\Parser\Traverser\Visitor;

$traverser = new Traverser([
    new class extends Visitor {
        public function enter(Node $node): ?Command
        {
            if ($node instanceof TemplateArgumentNode) {
                return Command::SKIP_CHILDREN;
            }

            echo \sprintf(" - %s\n", $node::class);

            return null;
        }
    }
]);

$traverser->traverse([$result]);
```
