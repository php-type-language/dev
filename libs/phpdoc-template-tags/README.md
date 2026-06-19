<a href="https://github.com/php-type-language" target="_blank">
    <img align="center" src="https://github.com/php-type-language/.github/blob/master/assets/dark.png?raw=true">
</a>

---

<p align="center">
    <a href="https://packagist.org/packages/type-lang/phpdoc-template-tags"><img src="https://poser.pugx.org/type-lang/phpdoc-template-tags/require/php?style=for-the-badge" alt="PHP 8.1+"></a>
    <a href="https://packagist.org/packages/type-lang/phpdoc-template-tags"><img src="https://poser.pugx.org/type-lang/phpdoc-template-tags/version?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/type-lang/phpdoc-template-tags"><img src="https://poser.pugx.org/type-lang/phpdoc-template-tags/v/unstable?style=for-the-badge" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/php-type-language/phpdoc-template-tags/blob/master/LICENSE"><img src="https://poser.pugx.org/type-lang/phpdoc-template-tags/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/php-type-language/phpdoc-template-tags/actions"><img src="https://github.com/php-type-language/phpdoc-template-tags/workflows/tests/badge.svg"></a>
</p>

Adds support of the PHPDoc standard DocBlock tags.

Read [documentation pages](https://typelang.dev) for more information.

## Installation

TypeLang PHPDoc Standard Tags is available as Composer repository and can
be installed using the following command in a root of your project:

```sh
composer require type-lang/phpdoc-template-tags
```

## Introduction

Adds support for advanced template annotations.

- [x] `@template` — `TypeLang\PHPDoc\Template\TemplateTagFactory`
- [x] `@template-covariant` — `TypeLang\PHPDoc\Template\TemplateCovariantTagFactory`
- [x] `@template-contravariant` — `TypeLang\PHPDoc\Template\TemplateContravariantTagFactory`
- [x] `@template-extends` (or `@extends`) — `TypeLang\PHPDoc\Template\TemplateExtendsTagFactory`
- [x] `@template-implements` (or `@implements`) — `TypeLang\PHPDoc\Template\TemplateImplementsTagFactory`
- [x] `@template-use` (or `@use`) — `TypeLang\PHPDoc\Template\TemplateUseTagFactory`

## Usage

```php
use TypeLang\PHPDoc\Parser;
use TypeLang\PHPDoc\Template;
use TypeLang\PHPDoc\Tag\Factory\TagFactory;

$tags = new TagFactory();

// Add support of template tags
$tags->register('template', new Template\TemplateTagFactory());
$tags->register('template-covariant', new Template\TemplateCovariantTagFactory());
$tags->register('template-contravariant', new Template\TemplateContravariantTagFactory());
$tags->register(['extends', 'template-extends'], new Template\TemplateExtendsTagFactory());
$tags->register(['implements', 'template-implements'], new Template\TemplateImplementsTagFactory());
$tags->register(['use', 'template-use'], new Template\TemplateUseTagFactory());

$docblock = (new Parser($tags))
    ->parse(<<<'PHPDOC'
        /**
         * @template T of object
         * @template-extends \Traversable<array-key, T>
         */
        PHPDOC);

var_dump($docblock);
```
