# StreamDumperVisitor

<primary-label ref="parser-component"/>
<show-structure for="chapter" depth="2"/>

To easily debug the structure and output of the AST, you can use the 
`StreamDumperVisitor` visitor.

```php
use TypeLang\Parser\Traverser;

Traverser::new([new Traverser\StreamDumperVisitor()])
    ->traverse([$result]);
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