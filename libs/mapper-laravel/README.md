# Laravel

Registers the mapper in a Laravel application: a default mapper and named
mappers extending its settings, `php artisan vendor:publish
--tag=type-lang-mapper-config` publishes the config.

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

The default mapper is `type-lang.mapper` (bound to `MapperInterface`), a
named one is `type-lang.mapper.api`.
