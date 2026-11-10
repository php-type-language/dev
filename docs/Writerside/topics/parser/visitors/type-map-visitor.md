# TypeMapVisitor

<primary-label ref="parser-component"/>
<link-summary>
Rewrite every `Name` occurrence throughout an AST via a callback — the
mechanism behind name resolution.
</link-summary>
<show-structure for="chapter" depth="2"/>

To replace short names with their <tooltip term="FQN">FQN</tooltip>
alternatives (or rewrite them any other way), use `TypeMapVisitor`. It calls
your callback for every `Name` it finds on a `NamedTypeNode` or
`CallableTypeNode` (via their `$name` property), on a
`ClassConstTypeNode`/`ClassConstMaskTypeNode` (via their `$class` property),
and on the namespace of a `ConstMaskTypeNode` (the last word of the namespace
is passed as a `Name`) — and, if the callback returns a `Name`, replaces it
**in place**.

```php
use TypeLang\Parser\Traverser;
use TypeLang\Parser\Traverser\TypeMapVisitor;
use TypeLang\Type\Name;
use TypeLang\Type\NamespaceName;

$type = (new TypeLang\Parser\TypeParser())
    ->parse('User|Admin');

// Replace all "User" occurrences with "App\Example\User"
$replacement = static function (Name $name): ?Name {
    if ($name->toLowerString() === 'user') {
        return new Name('User', new NamespaceName('App\\Example'));
    }

    return null;
};

Traverser::new([new TypeMapVisitor($replacement)])
    ->traverse([$type]);

// App\Example\User
echo $type->types[0]->name->toString(), "\n";
// Admin
echo $type->types[1]->name->toString(), "\n";
```

Returning `null` from the callback leaves that particular `Name` untouched
— as seen above for `Admin`, which doesn't match the condition.

The same rewrite reaches class-constant references too, since
`ClassConstTypeNode::$class` is also a `Name`:

```php
$type = (new TypeLang\Parser\TypeParser())
    ->parse('User::STATUS_ACTIVE');

Traverser::new([new TypeMapVisitor($replacement)])
    ->traverse([$type]);

echo $type->class->toString(); // App\Example\User
```

`TypeMapVisitor` is the visitor `TypeResolver` uses internally — reach for
[`TypeResolver`](type-resolver.md) directly if what you need is resolving
names against a set of PHP `use` statements; use `TypeMapVisitor` yourself
for anything more custom.
