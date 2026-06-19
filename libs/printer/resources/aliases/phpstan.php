<?php

declare(strict_types=1);

/**
 * List of builtin type aliases of phpstan 2.1.x-dev
 */
return [
    // int
    'integer' => 'int',
    'positive-int' => 'int',
    'negative-int' => 'int',
    'non-positive-int' => 'int',
    'non-negative-int' => 'int',
    'non-zero-int' => 'int',
    'int-mask' => 'int',
    'int-mask-of' => 'int',
    // string
    'lowercase-string' => 'string',
    'uppercase-string' => 'string',
    'literal-string' => 'string',
    'class-string' => 'string',
    'interface-string' => 'string',
    'trait-string' => 'string',
    'enum-string' => 'string',
    'callable-string' => 'string',
    'numeric-string' => 'string',
    'truthy-string' => 'string',
    'non-falsy-string' => 'string',
    'non-empty-string' => 'string',
    'non-empty-lowercase-string' => 'string',
    'non-empty-uppercase-string' => 'string',
    'non-empty-literal-string' => 'string',
    // bool
    'boolean' => 'bool',
    // float
    'double' => 'float',
    // array
    'associative-array' => 'array',
    'non-empty-array' => 'array',
    'callable-array' => 'array',
    'list' => 'array',
    'non-empty-list' => 'array',
    // object
    'callable-object' => 'object',
    // callable
    'pure-callable' => 'callable',
    'pure-closure' => '\Closure',
    // mixed
    'resource' => 'mixed',
    'open-resource' => 'mixed',
    'closed-resource' => 'mixed',
    'non-empty-mixed' => 'mixed',
    'key-of' => 'mixed',
    'value-of' => 'mixed',
    'template-type' => 'mixed',
    // never
    'noreturn' => 'never',
    'never-return' => 'never',
    'never-returns' => 'never',
    'no-return' => 'never',
    'empty' => 'never',
    // other
    'array-key' => 'int|string',
    'scalar' => 'bool|float|int|string',
    'non-empty-scalar' => 'bool|float|int|string',
    'empty-scalar' => 'bool|float|int|string',
    'number' => 'float|int',
    'numeric' => 'float|int|string',
    '__stringandstringable' => 'string|\Stringable',
];
