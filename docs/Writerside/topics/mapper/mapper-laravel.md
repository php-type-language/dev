# Laravel

<primary-label ref="mapper-laravel"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

The service provider registers mappers in the container of a Laravel 10+
application. It is discovered automatically.

```shell
composer require type-lang/mapper-laravel
php artisan vendor:publish --tag=type-lang-mapper-config
```

## Mappers

The default mapper is the `type-lang.mapper` singleton, bound to
`MapperInterface`, `DenormalizerInterface` and `NormalizerInterface`.

```php
use TypeLang\Mapper\DenormalizerInterface;

final class CreateUserController
{
    public function __invoke(Request $request, DenormalizerInterface $mapper): Response
    {
        $user = $mapper->denormalize($request->all(), CreateUser::class);
        // ...
    }
}
```

A named mapper takes the settings of the default one and overrides some of
them. It is the `type-lang.mapper.<name>` singleton.

```php
// config/type-lang-mapper.php
return [
    'naming' => 'snake_case',
    'mappers' => [
        'api' => ['strict' => true],
    ],
];
```

```php
app('type-lang.mapper.api')->denormalize('42', 'int');
// throws InvalidValueException: $: Value must be of type int, but "42" given

// to a class
$this->app->when(ApiController::class)
    ->needs(DenormalizerInterface::class)
    ->give('type-lang.mapper.api');
```

<note>
A setting a named mapper leaves <code>null</code> is the one of the default
mapper. The lists (types, hooks, readers, ...) of a named mapper go before
the ones of the default mapper.
</note>

## Configuration

```php
// config/type-lang-mapper.php
return [
    // see "Configuration" for the meaning of the options
    'strict' => false,
    'normalize_as_array' => true,
    'max_violations' => 1,
    'reject_extra_fields' => false,
    'expected_type_depth' => 1,
    'expected_type_max_fields' => null,

    // snake_case, camelCase, PascalCase, SCREAMING_SNAKE_CASE, kebab-case
    // or a NamingStrategyInterface service
    'naming' => null,

    // services, see "Extensions"
    'types' => [],
    'decorators' => [],
    'expanders' => [],
    'hooks' => [],
    'instantiators' => [],
    'accessors' => [],

    // CoercerInterface service by the type: int, float, string, bool, array
    'coercers' => [
        'float' => App\Mapper\DecimalCommaCoercer::class,
    ],

    'metadata' => [
        // MetadataReaderInterface services over the default readers
        'readers' => [],

        // config files or directories of them (yaml, yml, neon, json, php)
        'paths' => [config_path('mapping')],

        // PropertySelectorInterface service
        'selector' => null,

        'cache' => [
            // default = false with "app.debug", true otherwise
            'enabled' => null,
            // from the first one checked to the last one,
            // default = the default cache store
            'stores' => [
                ['store' => 'redis', 'prefix' => 'hot_'],
                'file',
            ],
        ],
    ],

    // mappers extending the default one, by the name
    'mappers' => [
        'api' => ['strict' => true],
    ],

    'profiler' => [
        // default = enabled with the Debugbar
        'debugbar' => null,
        // default = enabled with Telescope
        'telescope' => null,
    ],
];
```

The options are described in [Configuration](mapper-configuration.md),
[Naming Strategy](mapper-metadata-naming.md#naming-strategy),
[Config Files](mapper-metadata.md#config-files) and
[Caching](mapper-metadata.md#caching). The prefix of the cache keys is
`tlm_<mapper>_` by default.

<warning>
A wrong value fails on the first use of the mapper:
<code>Config "type-lang-mapper.mappers.api.max_violations" must be an
integer of at least 1, but int given</code>.
</warning>

## Extensions

An extension is a class name or a container id, resolved with the
container.

```php
// config/type-lang-mapper.php
return [
    'types' => [App\Mapper\UuidTypeBuilder::class],
    'hooks' => ['app.mapper.audit_hook'],
    'mappers' => [
        'api' => [
            'types' => [App\Mapper\MoneyTypeBuilder::class],
        ],
    ],
];
```

| Config key         | Interface                        |
|--------------------|----------------------------------|
| `types`            | `TypeBuilderInterface`           |
| `decorators`       | `TypeDecoratorInterface`         |
| `expanders`        | `TypeExpanderInterface`          |
| `hooks`            | `ClassHookInterface`             |
| `instantiators`    | `InstantiationStrategyInterface` |
| `accessors`        | `AccessStrategyInterface`        |
| `metadata.readers` | `MetadataReaderInterface`        |
| `coercers`         | `CoercerInterface`               |

A coercer of a named mapper replaces the one of the default mapper, see
[Custom Coercers](mapper-coercion.md#custom-coercers).

## Debugbar

With [barryvdh/laravel-debugbar](https://github.com/barryvdh/laravel-debugbar)
the bar gets a "mapper" tab: the calls of each mapper with their input,
output and violations, the place of the call, the built types and the
metadata reads. The calls are also shown on the timeline.

```php
// config/type-lang-mapper.php
return [
    'profiler' => [
        'debugbar' => false, // never
        'debugbar' => true,  // also with a disabled Debugbar, fails without one
    ],
];
```

## Telescope

With [Laravel Telescope](https://laravel.com/docs/telescope) each call of a
mapper is an entry of the "Logs" tab, tied to its request, job or command.

```
Mapper "api": denormalize(array{id: int}) failed in 0.42 ms

{
    "mapper": "api",
    "method": "denormalize",
    "type": "array{id: int}",
    "duration": 0.42,
    "error": "TypeLang\\Mapper\\Exception\\InvalidValueException: ...",
    "violations": ["$.id: Value must be of type int, but \"x\" given"],
    "called at": "/app/Http/Controllers/ApiController.php:42"
}
```

| Entry           | Level     | Tags                                                         |
|-----------------|-----------|--------------------------------------------------------------|
| succeeded call  | `debug`   | `type-lang-mapper`, `type-lang-mapper:api`                   |
| failed call     | `warning` | `type-lang-mapper`, `type-lang-mapper:api`, `type-lang-mapper:failed` |

<note>
The input and the output of a call are not recorded: Telescope stores the
entries in the database.
</note>

```php
// config/type-lang-mapper.php
return [
    'profiler' => [
        'telescope' => false, // never
        'telescope' => true,  // fails without Telescope
    ],
];
```
