<?php

declare(strict_types=1);

/**
 * List of builtin type aliases of phan 5.x-dev
 */
return [
    // int
    'integer' => 'int',
    'non-zero-int' => 'int',
    // string
    'callable-string' => 'string',
    'class-string' => 'string',
    'lowercase-string' => 'string',
    'non-empty-string' => 'string',
    'non-empty-lowercase-string' => 'string',
    'numeric-string' => 'string',
    // bool
    'boolean' => 'bool',
    // float
    'double' => 'float',
    // array
    'associative-array' => 'array',
    'callable-array' => 'array',
    'list' => 'array',
    'non-empty-array' => 'array',
    'non-empty-associative-array' => 'array',
    'non-empty-list' => 'array',
    // object
    'callable-object' => 'object',
    // mixed
    'non-empty-mixed' => 'mixed',
    'non-null-mixed' => 'mixed',
    'phan-intersection-type' => 'mixed',
    'resource' => 'mixed',
    // never
    'no-return' => 'never',
    'never-return' => 'never',
    'never-returns' => 'never',
    // other
    'array-key' => 'int|string',
    'scalar' => 'bool|float|int|string',
];
