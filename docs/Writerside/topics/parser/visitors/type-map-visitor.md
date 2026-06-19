# TypeMapVisitor

<primary-label ref="parser-component"/>
<show-structure for="chapter" depth="2"/>

To convert short names to their <tooltip term="FQN">FQN</tooltip> alternatives,
you can use `TypeMapVisitor`.

```php
use TypeLang\Parser\Traverser;

// Replace all "User" ocurrences to "App\Example\User"
$replacement = static function (Name $name): ?Name {
    if ($name->toLowerString() === 'user') {
        return new Name('App\\Example\\User');
    }
    
    return null;
};


Traverser::new([new Traverser\TypeMapVisitor($replacement)])
    ->traverse([$result]);
```