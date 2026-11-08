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
- PHP 8.1+

## Usage

A type is a tree of typed nodes instead of a string, so tools can inspect and
change it without parsing text. You usually get the tree from the parser, but
it can be built by hand as well. Every node also keeps the `$offset` of its
token in the source.

```php
// Example<string, int, *>|null
$type = new UnionTypeNode([
    new NamedTypeNode(
        name: new Name('Example'),
        arguments: new TemplateListNode([
            new TypedTemplateArgumentNode(
                new NamedTypeNode(new Name('string')),
            ),
            new TypedTemplateArgumentNode(
                new NamedTypeNode(new Name('int')),
            ),
            new WildcardTemplateArgumentNode()
        ]),
    ),
    new NullLiteralNode(),
]);

// Read the tree
$type[0]->name->value;                    // 'array'
$type[0]->arguments[1]->value->name->value; // 'int'

// Change it: the tree is mutable, names and literals are immutable values
$type[0]->name = new Name('iterable');
```

The package covers the full type grammar: unions and intersections, generics,
shapes, callables, conditional types, class constants and their masks, and
literals. See the [documentation](https://typelang.dev) for the node reference.
