# StringDumperVisitor

<primary-label ref="parser-component"/>
<link-summary>
Render an AST as an indented, human-readable string — useful for debugging,
snapshot tests, and log messages.
</link-summary>
<show-structure for="chapter" depth="2"/>

To capture the structure of an AST into a string, use `StringDumperVisitor` —
a [`DumperVisitor`](dumper-visitor.md) that buffers into an internal
`$output` string instead of writing to a stream.

```php
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\StringDumperVisitor;

$type = new TypeLang\Parser\TypeParser()
    ->parse('array<array-key, object{ key?: int<0, max> }>');

$visitor = new StringDumperVisitor();

Traverser::new([$visitor])
    ->traverse([$type]);

echo $visitor->output;
```

You can see the following text as the output result:

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

This is convenient for snapshot-style unit tests: parse a type, dump it, and
assert on the string instead of comparing whole object graphs.

```php
public function testExampleType(): void
{
    $visitor = new StringDumperVisitor();

    Traverser::new([$visitor])
        ->traverse([
            new TypeLang\Parser\TypeParser()
                ->parse('int|null'),
        ]);

    self::assertSame(<<<'AST'
        UnionTypeNode
          NamedTypeNode
            Name(int)
              Identifier(int)
          Literal\NullLiteralNode(null)
        AST, $visitor->output);
}
```

The visitor is reusable across multiple `traverse()` calls — call
`$visitor->reset()` (or just let the next `traverse()` call `before()`
implicitly) to clear `$output` in between.
