<?php

declare(strict_types=1);

/**
 * Settings of the default mapper and of the named mappers extending it. A
 * setting left null is inherited, from the default mapper by a named one,
 * the lists of a named mapper go before the ones of the default mapper.
 */
return [
    // Whether a value must match the type exactly, without coercion
    'strict' => null,

    // Whether an object is normalized to an array or to a stdClass
    'normalize_as_array' => null,

    // Number of violations a denormalization collects before it fails
    'max_violations' => null,

    // Whether a field no property takes is a violation
    'reject_extra_fields' => null,

    // Number of levels of classes described by their fields in messages
    'expected_type_depth' => null,

    // Number of fields of a class shown in messages, all of them by default
    'expected_type_max_fields' => null,

    // Built-in naming strategy (snake_case, camelCase, PascalCase,
    // SCREAMING_SNAKE_CASE, kebab-case) or a service
    'naming' => null,

    // Services of the extensions, the classes or the container ids
    'types' => [],
    'decorators' => [],
    'expanders' => [],
    'hooks' => [],
    'instantiators' => [],
    'accessors' => [],

    // Coercers by the type: int, float, string, bool, array
    'coercers' => [],

    'metadata' => [
        // Metadata readers over the default ones
        'readers' => [],

        // Mapping config files or directories of them (yaml, neon, json, php)
        'paths' => [],

        // Service choosing the mapped properties of a class
        'selector' => null,

        'cache' => [
            // Disabled in debug mode by default
            'enabled' => null,

            // Cache stores from the first one checked to the last one, the
            // default store in case of none:
            // [['store' => 'redis', 'prefix' => 'hot_'], ['store' => 'file']]
            'stores' => [],
        ],
    ],

    // Mappers extending the default one, by the name:
    // 'api' => ['strict' => true, 'types' => [App\Mapper\MoneyTypeBuilder::class]]
    'mappers' => [],

    // Mapper tab of the Laravel Debugbar, enabled with the Debugbar by default
    'profiler' => null,
];
