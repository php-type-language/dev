<a href="https://github.com/php-type-language" target="_blank">
    <img align="center" src="https://github.com/php-type-language/.github/blob/master/assets/dark.png?raw=true">
</a>

---

<p align="center">
    <a href="https://packagist.org/packages/type-lang/printer"><img src="https://poser.pugx.org/type-lang/printer/require/php?style=for-the-badge" alt="PHP 8.1+"></a>
    <a href="https://packagist.org/packages/type-lang/printer"><img src="https://poser.pugx.org/type-lang/printer/version?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/type-lang/printer"><img src="https://poser.pugx.org/type-lang/printer/v/unstable?style=for-the-badge" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/php-type-language/printer/blob/master/LICENSE"><img src="https://poser.pugx.org/type-lang/printer/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/php-type-language/printer/actions"><img src="https://github.com/php-type-language/printer/workflows/tests/badge.svg"></a>
</p>

Reference implementation for TypeLang Printer.

## Resources

- [Documentation](https://typelang.dev)

## Installation

TypeLang Printer is available as Composer repository and can be installed
using the following command in a root of your project:

```sh
composer require type-lang/printer
```

## Quick Start

```php
$parser = new \TypeLang\Parser\Parser();
$type = $parser->parseType(<<<'PHP'
    array{
        field1: (callable(Example,int):mixed),
        field2: list<Some>,
        field3: iterable<array-key, array{int, non-empty-string}>,
        Some::CONST_*,
        "\njson_flags": \JSON_*,
        ...
    }
    PHP);

// Print Statement

$native = new \TypeLang\Printer\NativeTypePrinter();
echo $native->print($type);

// Expected Output:
// array

$phpdoc = new \TypeLang\Printer\PrettyPrinter();
echo $phpdoc->print($type);

// Expected Output:
// array{
//     field1: callable(Example, int): mixed,
//     field2: list<Some>,
//     field3: iterable<array-key, array{
//         int,
//         non-empty-string
//     }>,
//     Some::CONST_*,
//     "\njson_flags": \JSON_*,
//     ...
// }
```
