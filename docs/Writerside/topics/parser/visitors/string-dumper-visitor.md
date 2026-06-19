# StringDumperVisitor

<primary-label ref="parser-component"/>
<show-structure for="chapter" depth="2"/>

To easily debug the structure and capture AST structure, you can use the 
`StringDumperVisitor` visitor.

```php
use TypeLang\Parser\Traverser;

$visitor = new Traverser\StringDumperVisitor();

Traverser::new([$visitor])
    ->traverse([$result]);

echo $visitor->getOutput();
```

> You can see the following text as the output result:
> 
> ```yaml
> Stmt\NamedTypeNode
>   Name(array)
>   Stmt\Template\ArgumentsListNode
>     Stmt\Template\ArgumentNode
>       Stmt\NamedTypeNode
>         Name(array-key)
>     Stmt\Template\ArgumentNode
>       Stmt\NamedTypeNode
>         Name(object)
>         Stmt\Shape\FieldsListNode(unsealed)
>           Stmt\Shape\NamedFieldNode(required)
>             Stmt\NamedTypeNode
>               Name(int)
>               Stmt\Template\ArgumentsListNode
>                 Stmt\Template\ArgumentNode
>                   Literal\IntLiteralNode(0)
>                 Stmt\Template\ArgumentNode
>                   Stmt\NamedTypeNode
>                     Name(max)
>             Identifier(key)
> ```