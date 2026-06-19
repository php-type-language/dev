<?php

declare(strict_types=1);

/**
 * List of builtin type aliases of psalm 5.x-dev
 */
return [
    // int
    'integer' => 'int',
    'positive-int' => 'int',
    'non-positive-int' => 'int',
    'negative-int' => 'int',
    'non-negative-int' => 'int',
    'literal-int' => 'int',
    // string
    'non-empty-string' => 'string',
    'truthy-string' => 'string',
    'non-falsy-string' => 'string',
    'lowercase-string' => 'string',
    'non-empty-lowercase-string' => 'string',
    'class-string' => 'string',
    'interface-string' => 'string',
    'enum-string' => 'string',
    'trait-string' => 'string',
    'callable-string' => 'string',
    'numeric-string' => 'string',
    'literal-string' => 'string',
    'non-empty-literal-string' => 'string',
    // bool
    'boolean' => 'bool',
    // float
    'double' => 'float',
    'real' => 'float',
    // array
    'associative-array' => 'array',
    'non-empty-array' => 'array',
    'callable-array' => 'array',
    'list' => 'array',
    'non-empty-list' => 'array',
    'class-string-map' => 'array',
    'public-properties-of' => 'array',
    'protected-properties-of' => 'array',
    'private-properties-of' => 'array',
    'properties-of' => 'array',
    // object
    'callable-object' => 'object',
    'stringable-object' => '\Stringable',
    // callable
    'pure-callable' => 'callable',
    // mixed
    'resource' => 'mixed',
    'resource (closed)' => 'mixed',
    'closed-resource' => 'mixed',
    'non-empty-mixed' => 'mixed',
    'key-of' => 'mixed',
    // never
    'never-return' => 'never',
    'never-returns' => 'never',
    'no-return' => 'never',
    'empty' => 'never',
    // other
    'array-key' => 'int|string',
    'scalar' => 'bool|float|int|string',
    'non-empty-scalar' => 'bool|float|int|string',
    'empty-scalar' => 'bool|float|int|string',
];
