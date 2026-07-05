# StreamDumperVisitor

<primary-label ref="parser-component"/>
<link-summary>
Render an AST as an indented, human-readable dump directly to a stream —
`STDERR` by default.
</link-summary>
<show-structure for="chapter" depth="2"/>

To print the structure of an AST straight to a stream while debugging, use
`StreamDumperVisitor` — a [`DumperVisitor`](dumper-visitor.md) that writes
to a PHP stream resource (`php://stderr` by default) instead of buffering
into a string.

```php
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\StreamDumperVisitor;

$type = (new TypeLang\Parser\TypeParser())
    ->parse('array<array-key, object{ key?: int<0, max> }>');

Traverser::new([new StreamDumperVisitor()])->traverse([$type]);
```

You can see the following text printed to `STDERR`:

```yaml
NamedTypeNode
  Name(array)
    Identifier(array)
  Template\TemplateArgumentListNode
    Template\TemplateArgumentNode
      NamedTypeNode
        Name(array-key)
          Identifier(array-key)
    Template\TemplateArgumentNode
      NamedTypeNode
        Name(object)
          Identifier(object)
        Shape\FieldsListNode(sealed)
          Shape\NamedFieldNode(optional)
            Identifier(key)
            NamedTypeNode
              Name(int)
                Identifier(int)
              Template\TemplateArgumentListNode
                Template\TemplateArgumentNode
                  Literal\IntLiteralNode(0)
                Template\TemplateArgumentNode
                  NamedTypeNode
                    Name(max)
                      Identifier(max)
```

## Writing Elsewhere

The second constructor argument is any stream URI accepted by `fopen()` —
point it at a file to keep a persistent trace of every type parsed during a
debugging session:

```php
$visitor = new StreamDumperVisitor(
    stream: __DIR__ . '/parsed-types.log',
);

Traverser::new([$visitor])
    ->traverse([$type]);
```

Since a plain string is more convenient than a file when the goal is a
one-off assertion or a log message, consider
[`StringDumperVisitor`](string-dumper-visitor.md) instead in those cases.
