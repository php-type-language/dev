<a href="https://github.com/php-type-language" target="_blank">
    <img align="center" src="https://github.com/php-type-language/.github/blob/master/assets/dark.png?raw=true">
</a>

---

**TypeLang** is a declarative type language inspired by static analyzers like
[PHPStan](https://phpstan.org/) and [Psalm](https://psalm.dev/docs/). It
describes the type syntax found in PHPDoc comments (`int[]`, `array<string, T>`,
`Foo::CONST_*`, `T is U ? A : B`, ...) and provides a set of components to work
with it.

This is the **development monorepo** for all TypeLang components. Each package
listed below is also published as a standalone, read-only repository via
[git subtree split](https://www.atlassian.com/git/tutorials/git-subtree).

- Full documentation is available at [typelang.dev](https://typelang.dev).
- PHP Type Language specification is [available here](https://typelang.dev/static/spec.html).

## Packages

| Package                                                                                     | Description                                                                    | Tests                                                                                                                                                                                                                                        |
|---------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [`type-lang/mapper`](https://packagist.org/packages/type-lang/mapper)                       | Maps variable types to other types.                                            | [![Tests](https://img.shields.io/github/actions/workflow/status/php-type-language/mapper/tests.yml?label=Tests&style=flat-square&logo=unpkg)](https://github.com/php-type-language/mapper/actions/workflows/tests.yml)                       |
| [`type-lang/parser`](https://packagist.org/packages/type-lang/parser)                       | Parses TypeLang syntax into an AST, validating the grammar.                    | [![Tests](https://img.shields.io/github/actions/workflow/status/php-type-language/parser/tests.yml?label=Tests&style=flat-square&logo=unpkg)](https://github.com/php-type-language/parser/actions/workflows/tests.yml)                       |
| [`type-lang/phpdoc`](https://packagist.org/packages/type-lang/phpdoc)                       | Parses `/** ... */` DocBlock comments into a graph of description and tags.    | [![Tests](https://img.shields.io/github/actions/workflow/status/php-type-language/phpdoc/tests.yml?label=Tests&style=flat-square&logo=unpkg)](https://github.com/php-type-language/phpdoc/actions/workflows/tests.yml)                       |
| [`type-lang/printer`](https://packagist.org/packages/type-lang/printer)                     | Renders AST nodes back into their string representation.                       | [![Tests](https://img.shields.io/github/actions/workflow/status/php-type-language/printer/tests.yml?label=Tests&style=flat-square&logo=unpkg)](https://github.com/php-type-language/printer/actions/workflows/tests.yml)                     |
| [`type-lang/reader`](https://packagist.org/packages/type-lang/reader)                       | Builds AST nodes from types exposed by PHP Reflection objects.                 | [![Tests](https://img.shields.io/github/actions/workflow/status/php-type-language/reader/tests.yml?label=Tests&style=flat-square&logo=unpkg)](https://github.com/php-type-language/reader/actions/workflows/tests.yml)                       |
| [`type-lang/types`](https://packagist.org/packages/type-lang/types)                         | AST node classes (`TypeLang\Type\*`) — the shared vocabulary of the ecosystem. | [![Tests](https://img.shields.io/github/actions/workflow/status/php-type-language/types/tests.yml?label=Tests&style=flat-square&logo=unpkg)](https://github.com/php-type-language/types/actions/workflows/tests.yml)                         |

## Development

This repository contains all components together. Changes are contributed here
and then split into the individual read-only package repositories.

```sh
git clone https://github.com/php-type-language/dev.git
cd dev
composer install
```

Common tasks:

```sh
composer test:unit     # run the PHPUnit test suite
composer linter:check  # run PHPStan static analysis
composer phpcs:check   # check the code style (php-cs-fixer, dry-run)
composer phpcs:fix     # apply the code style fixes
```

## License

TypeLang is licensed under the [MIT License](LICENSE).
