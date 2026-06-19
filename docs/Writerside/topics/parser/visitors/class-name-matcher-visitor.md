# ClassNameMatcherVisitor

<primary-label ref="parser-component"/>
<show-structure for="chapter" depth="2"/>

To search for the first suitable node for an instance of a class, you can
use the `ClassNameMatcherVisitor` visitor.

```php
use TypeLang\Parser\Node\Name;
use TypeLang\Parser\Traverser;

Traverser::new([
    $finder = new Traverser\ClassNameMatcherVisitor(Name::class),
])
    ->traverse([$result]);

var_dump($finder->getFoundNode());
```

> You can see the following text as the output result:
> 
> ```php
> TypeLang\Parser\Node\Name {
>   +offset: 0
>   -parts: array:1 [
>     0 => TypeLang\Parser\Node\Identifier {
>       +offset: 0
>       +value: "array"
>     }
>   ]
> }
> ```
