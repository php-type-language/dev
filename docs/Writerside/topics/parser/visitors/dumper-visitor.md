# DumperVisitor

<primary-label ref="parser-component"/>
<link-summary>
The abstract base behind `StreamDumperVisitor` and `StringDumperVisitor` —
extend it to render a dump anywhere else.
</link-summary>
<show-structure for="chapter" depth="2"/>

`DumperVisitor` is the abstract base shared by
[`StreamDumperVisitor`](stream-dumper-visitor.md) and
[`StringDumperVisitor`](string-dumper-visitor.md). It implements the whole
traversal side of rendering — indentation tracking and node formatting — and
leaves only the sink itself abstract:

```php
abstract class DumperVisitor extends Visitor
{
    public const string DEFAULT_SIMPLIFIED_NODE_NAMESPACE 
        = 'TypeLang\\Type\\';

    public function __construct(
        string $simplifyNodeNamespace 
            = self::DEFAULT_SIMPLIFIED_NODE_NAMESPACE,
    ) {}

    abstract protected function write(string $data): void;
}
```

Each visited node is rendered as `<indent><ShortClassName>[(value)]`, one
line per node: the class name has `$simplifyNodeNamespace` stripped off its
front, and if the node implements `\Stringable`, its string representation
is appended in parentheses.

## Writing a Custom Sink

Extend `DumperVisitor` and implement `write()` to render anywhere you like —
into an array of lines, a PSR logger, a buffer shared with other output, etc:

```php
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\DumperVisitor;

final class LineCollectorDumperVisitor extends DumperVisitor
{
    /** @var list<string> */
    public array $lines = [];

    protected function write(string $data): void
    {
        $this->lines[] = \rtrim($data, "\n");
    }
}

$type = new TypeLang\Parser\TypeParser()
    ->parse('int|null');

$visitor = new LineCollectorDumperVisitor();
Traverser::new([$visitor])->traverse([$type]);

var_dump($visitor->lines);
```

```php
array(5) {
  [0]=> string(13) "UnionTypeNode"
  [1]=> string(15) "  NamedTypeNode"
  [2]=> string(13) "    Name(int)"
  [3]=> string(21) "      Identifier(int)"
  [4]=> string(31) "  Literal\NullLiteralNode(null)"
}
```

## Disabling Namespace Simplification

Pass an empty string (or any prefix that won't match) as
`$simplifyNodeNamespace` to render fully-qualified class names instead:

```php
$visitor = new LineCollectorDumperVisitor(
    simplifyNodeNamespace: '',
);
```

```
TypeLang\Type\UnionTypeNode
  TypeLang\Type\NamedTypeNode
    TypeLang\Type\Name(int)
      TypeLang\Type\Identifier(int)
  TypeLang\Type\Literal\NullLiteralNode(null)
```
