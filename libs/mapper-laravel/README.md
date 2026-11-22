<a href="https://github.com/php-type-language" target="_blank">
    <img align="center" src="https://github.com/php-type-language/.github/blob/master/assets/dark.png?raw=true">
</a>

<p align="center">
    <a href="https://packagist.org/packages/type-lang/mapper-laravel"><img src="https://poser.pugx.org/type-lang/mapper-laravel/require/php?style=for-the-badge" alt="PHP 8.1+"></a>
    <a href="https://packagist.org/packages/type-lang/mapper-laravel"><img src="https://poser.pugx.org/type-lang/mapper-laravel/version?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/type-lang/mapper-laravel"><img src="https://poser.pugx.org/type-lang/mapper-laravel/v/unstable?style=for-the-badge" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/php-type-language/mapper-laravel/blob/master/LICENSE"><img src="https://poser.pugx.org/type-lang/mapper-laravel/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/php-type-language/mapper-laravel/actions/workflows/tests.yml"><img src="https://img.shields.io/github/actions/workflow/status/php-type-language/mapper-laravel/tests.yml?label=Tests&style=flat-square&logo=unpkg"></a>
</p>

---

Laravel integration of the [TypeLang mapper](https://packagist.org/packages/type-lang/mapper):
the service provider registers the mappers in the container and configures
them from `config/type-lang-mapper.php`.

Full documentation is available at
[typelang.dev](https://typelang.dev/mapper/integrations/laravel/).

## Installation

Install the package via [Composer](https://getcomposer.org):

```sh
composer require type-lang/mapper-laravel
```

The service provider is discovered automatically. Publish the config to
change the settings:

```sh
php artisan vendor:publish --tag=type-lang-mapper-config
```

**Requirements:**
- PHP 8.1+
- Laravel 10+

## Usage

The default mapper is injected by `MapperInterface`, `DenormalizerInterface`
or `NormalizerInterface`:

```php
use TypeLang\Mapper\MapperInterface;

final class CreateUserController
{
    public function __invoke(Request $req, MapperInterface $mapper): Response
    {
        $user = $mapper->denormalize($req->all(), UserRequestDTO::class);
        // ...
    }
}
```

A named mapper takes the settings of the default one and overrides some of
them:

```php
// config/type-lang-mapper.php
return [
    'naming' => 'snake_case',
    'types' => [App\Mapper\UuidTypeBuilder::class],
    'mappers' => [
        'api' => [
            'strict' => true,
            'types' => [App\Mapper\MoneyTypeBuilder::class],
        ],
    ],
];
```

```php
app('type-lang.mapper')->denormalize('42', 'int');
// 42

app('type-lang.mapper.api')->denormalize('42', 'int');
// throws InvalidValueException: $: Value must be of type int, but "42" given
```
