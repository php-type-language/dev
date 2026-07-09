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

`TypeResolver` is an immutable builder. You register the imports it should
resolve against — through the fluent `withTypeImport()` / `withTypeImportAs()`
methods (or the constructor) — and then call `resolve()`. It walks the whole
node graph (via the same [`Traverser`](visitors.md) machinery used by
[`TypeMapVisitor`](type-map-visitor.md)) and rewrites every matching
`TypeLang\Type\Name` it finds **in place**.

```php
final readonly class TypeResolver
{
    public function withTypeImport(string $name): self;

    public function withTypeImportAs(string $name, string $alias): self;

    public function withTypeImportsFromClass(\ReflectionClass $class): self;

    public function withTypeImportsFromFunction(\ReflectionFunctionAbstract $function): self;

    public function resolve(TypeNode $type): TypeNode;
}
```

> `resolve()` **mutates** and returns the **same** `$type` instance you passed
> in — it is not a copy. Clone the AST beforehand (or re-parse) if you need
> to keep the original, unresolved names around.
> {style="warning"}

## Resolving Against PHP `use` Statements

The most common use case is resolving relative names the same way PHP resolves
class references against the `use` statements of the file a phpdoc comment lives
in.

Given PHP source that declares:

```php
use TypeLang\Parser\Node;
use TypeLang\Parser\Exception as Error;
```

register the plain imports with `withTypeImport()` and the aliased ones with
`withTypeImportAs()`:

```php
use TypeLang\Parser\TypeResolver;

$resolver = new TypeResolver()
    // use TypeLang\Parser\Node;
    ->withTypeImport('TypeLang\Parser\Node')
    // use TypeLang\Parser\Exception as Error;
    ->withTypeImportAs('TypeLang\Parser\Exception', 'Error');
```

Applying it resolves the first segment of every name that matches one of the
imports (case-insensitively, just like PHP) and merges in the rest of the name
unchanged:

```php
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeResolver;

// parse array shape with 2 named types
$statement = new TypeParser()
    ->parse(<<<'PHP'
        array{
            Node,
            Error\SemanticException
        }
        PHP);

// resolve type names
$statement = new TypeResolver()
    ->withTypeImport('TypeLang\Parser\Node')
    ->withTypeImportAs('TypeLang\Parser\Exception', 'Error')
    ->resolve($statement);

foreach ($statement->fields->items as $field) {
    echo $field->type->name->toString(), "\n";
}

// Expected Output:
//   TypeLang\Parser\Node
//   TypeLang\Parser\Exception\SemanticException
```

Because `TypeResolver` is immutable, `withTypeImport()` and
`withTypeImportAs()` return a new instance each time and never mutate the
receiver — the same resolver can be safely shared and extended for different
contexts.

### Passing Imports Through the Constructor

If you already have the whole import list assembled, hand it to the constructor
directly instead of chaining `with*` calls. Plain imports are listed as values
and aliased ones as `alias => target` pairs:

```php
use TypeLang\Parser\TypeResolver;

$resolver = new TypeResolver([
    // use TypeLang\Parser\Node;
    'TypeLang\Parser\Node',
    // use TypeLang\Parser\Exception as Error;
    'Error' => 'TypeLang\Parser\Exception',
]);

$statement = $resolver->resolve($statement);
```

For a plain import the alias is inferred from the last segment of the name
(`TypeLang\Parser\Node` becomes reachable as `Node`), which mirrors how a
`use` statement without an explicit `as` behaves.

## Reading Imports From Reflection

Instead of listing imports by hand, `TypeResolver` can read them straight from
the source file of a reflected class or function — resolving type names exactly
as PHP would inside that element.

Given a class whose file declares:

```php
namespace App;

use TypeLang\Parser\Node;
use TypeLang\Parser\Exception as Error;

final class Example {}
```

`withTypeImportsFromClass()` registers both of its imports:

```php
use TypeLang\Parser\TypeParser;
use TypeLang\Parser\TypeResolver;

$statement = new TypeParser()
    ->parse('Node|Error\SemanticException');

$statement = new TypeResolver()
    ->withTypeImportsFromClass(new ReflectionClass(App\Example::class))
    ->resolve($statement);

foreach ($statement->statements as $type) {
    echo $type->name->toString(), "\n";
}

// Expected Output:
//   TypeLang\Parser\Node
//   TypeLang\Parser\Exception\SemanticException
```

`withTypeImportsFromFunction()` does the same for a free function or a method —
both are a `ReflectionFunctionAbstract` — reading the imports of the file the
function is declared in:

```php
use TypeLang\Parser\TypeResolver;

$resolver = new TypeResolver()
    ->withTypeImportsFromFunction(new ReflectionFunction('App\example'));
```

> Only the file header, up to the element's own declaration, is scanned, and
> only real `use` imports are collected — a trait `use` inside a class body or
> a closure `use (...)` capture is never mistaken for an import.
> {style="note"}

## Custom Name Rewriting

`TypeResolver` is intentionally scoped to `use`-statement semantics. When you
need to rewrite names some other way — against a runtime class map, a DI
container, a PSR-4 lookup, and so on — drop down to
[`TypeMapVisitor`](type-map-visitor.md), which calls a callback of your own for
every `Name` in the AST. `TypeResolver` is simply the ready-made configuration
of that visitor for the most common case.
