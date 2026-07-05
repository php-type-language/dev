# Name Resolution

<primary-label ref="parser-component"/>
<link-summary>
Rewrite every `Name` node in an already-parsed AST — typically to resolve
short/relative class names against a set of PHP `use` statements.
</link-summary>
<show-structure for="chapter" depth="2"/>

The parser has no notion of namespaces, `use` statements, or autoloading — a
type such as `Collection<User>` is parsed exactly as written, with `Collection`
and `User` staying relative names. Resolving what they actually refer to is a
separate step, deliberately left up to the caller, and `TypeLang\Parser\TypeResolver`
is the utility for it.

## How It Works

`TypeResolver::resolve()` walks the whole node graph (via the same
[`Traverser`](visitors.md) machinery used for [`TypeMapVisitor`](type-map-visitor.md))
and, for every `TypeLang\Type\Name` it finds, calls your `$transform`
callback. If the callback returns a `Name`, it replaces the original one **in
place**; if it returns `null`, the original name is left untouched.

```php
interface TypeResolverInterface
{
    /**
     * @param callable(Name): (Name|null) $transform
     */
    public function resolve(
        TypeNode $type, 
        callable $transform,
    ): TypeNode;
}
```

```php
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeResolver;
use TypeLang\Type\Name;


$type = new TypeParser()
    ->parse('User|Admin');


new TypeResolver()
    ->resolve($type, static function (Name $name): ?Name {
        if ($name->toLowerString() === 'user') {
            return Name::createFromString('App\\Example\\User');
        }

        return null;
    });


// App\Example\User
echo $type->statements[0]->name->toString();
// Admin
echo $type->statements[1]->name->toString();
```

> `resolve()` **mutates** and returns the **same** `$type` instance you passed
> in — it is not a copy. Clone the AST beforehand (or re-parse) if you need
> to keep the original, unresolved names around.
> {style="warning"}

## Resolving Against PHP `use` Statements

The most common use case — resolving relative names the same way PHP resolves
class references against the `use` statements of the file a phpdoc comment
lives in — is covered by the ready-made `TypeResolver\PhpUseStatementsTransformer`,
an implementation of `TransformerInterface` (`__invoke(Name $name): ?Name`).

Given PHP source that declares:

```php
use TypeLang\Parser\Node;
use TypeLang\Parser\Exception as Error;
```

build the equivalent transformer by listing the plain imports as values and
the aliased ones as `alias => target` pairs:

```php
use TypeLang\Parser\TypeResolver\PhpUseStatementsTransformer;

$transformer = new PhpUseStatementsTransformer([
    // use TypeLang\Parser\Node;
    'TypeLang\Parser\Node',
    // use TypeLang\Parser\Exception as Error;
    'Error' => 'TypeLang\Parser\Exception',
]);
```

Applying it resolves every first segment of a name that matches one of the
imports, and merges in the rest of the name unchanged:

```php
use TypeLang\Parser\TypeResolver\PhpUseStatementsTransformer;

// parse array shape with 2 named types
$sourceAst = $parser->parse(<<<'PHP'
    array{ 
        Node,
        Error\SemanticException
    }
    PHP);

// resolve type names
new TypeResolver()
    ->resolve($sourceAst, new PhpUseStatementsTransformer([
        // use TypeLang\Parser\Node;
        'TypeLang\Parser\Node',
        // use TypeLang\Parser\Exception as Error;
        'Error' => 'TypeLang\Parser\Exception',
    ]));

foreach ($sourceAst->fields->items as $field) {
    echo $field->type->name->toString(), "\n";
}

// Expected Output:
//   TypeLang\Parser\Node
//   TypeLang\Parser\Exception\SemanticException
```

## Writing a Custom Transformer

Because `TransformerInterface` is a single-method contract, any callable
works — a closure, an invokable object backed by a symbol table, a PSR-4
autoloader lookup, and so on. `PhpUseStatementsTransformer` is just the
built-in implementation for the most common case; nothing stops you from
implementing `TransformerInterface` yourself for anything more specific your
application needs (resolving against a runtime class map, a container, etc).
