<a href="https://github.com/php-type-language" target="_blank">
    <img align="center" src="https://github.com/php-type-language/.github/blob/master/assets/dark.png?raw=true">
</a>

<p align="center">
    <a href="https://packagist.org/packages/type-lang/types"><img src="https://poser.pugx.org/type-lang/types/require/php?style=for-the-badge" alt="PHP 8.1+"></a>
    <a href="https://packagist.org/packages/type-lang/types"><img src="https://poser.pugx.org/type-lang/types/version?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/type-lang/types"><img src="https://poser.pugx.org/type-lang/types/v/unstable?style=for-the-badge" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/php-type-language/types/blob/master/LICENSE"><img src="https://poser.pugx.org/type-lang/types/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/php-type-language/types/actions"><img src="https://github.com/php-type-language/types/workflows/tests/badge.svg"></a>
</p>

---

The AST node classes (`TypeLang\Type\*`) for **TypeLang** — a declarative type
language inspired by static analyzers like [PHPStan](https://phpstan.org/) and
[Psalm](https://psalm.dev/docs/).

These plain, dependency-free DTOs are the shared vocabulary of the TypeLang
ecosystem: the [parser](https://packagist.org/packages/type-lang/parser) produces
them, the [printer](https://packagist.org/packages/type-lang/printer) renders
them, and the [reader](https://packagist.org/packages/type-lang/reader) builds
them from Reflection.

Full documentation is available at [typelang.dev](https://typelang.dev).

## Installation

Install the package via [Composer](https://getcomposer.org):

```sh
composer require type-lang/types
```

**Requirements:** 
- PHP 8.4+

## Usage

Every node extends the abstract `Node` class and exposes an `$offset` (byte
offset of the token in the original source). You usually get nodes from the
parser, but they can also be constructed by hand.

### Identifiers and Names

```php
use TypeLang\Type\Identifier;
use TypeLang\Type\Name;

$id = Identifier::createFromString('non-empty-string');
$id->value;     // 'non-empty-string'
$id->isVirtual; // true  (contains "-", e.g. "array-key", "positive-int")
$id->isBuiltin; // false (e.g. "int", "bool", "null")
$id->isSpecial; // false (e.g. "self", "static", "parent")

$name = Name::createFromString('\TypeLang\Type\Node');
$name->isFullyQualified; // true
$name->first->value;     // 'TypeLang'
$name->last->value;      // 'Node'
```

### Building Type Nodes

```php
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\NullableTypeNode;
use TypeLang\Type\UnionTypeNode;
use TypeLang\Type\TemplateArgumentListNode;
use TypeLang\Type\TemplateArgumentNode;

// int
new NamedTypeNode(Name::createFromString('int'));

// ?string
new NullableTypeNode(new NamedTypeNode(Name::createFromString('string')));

// int|string|null  (nested unions of the same kind are flattened)
new UnionTypeNode(
    new NamedTypeNode(Name::createFromString('int')),
    new NamedTypeNode(Name::createFromString('string')),
    new NamedTypeNode(Name::createFromString('null')),
);

// array<string, int>
new NamedTypeNode(
    name: Name::createFromString('array'),
    arguments: new TemplateArgumentListNode([
        new TemplateArgumentNode(new NamedTypeNode(Name::createFromString('string'))),
        new TemplateArgumentNode(new NamedTypeNode(Name::createFromString('int'))),
    ]),
);
```

### Node Lists

All list containers (shape fields, template arguments, callable parameters, ...)
extend `NodeList` and implement `Countable`, `ArrayAccess` and `IteratorAggregate`:

```php
count($list);   // number of items
$list[0];       // item by offset
$list->first;   // first item
$list->last;    // last item
foreach ($list as $item) { /* ... */ }
```

The package covers the full type grammar — unions and intersections, callables,
conditional (ternary) expressions, class-constant masks, literals, shape fields
and attributes. See the [documentation](https://typelang.dev) for the complete
node reference.
