# Writing Metadata

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="3"/>

Every option can be written in any [metadata source](mapper-metadata.md).
Choose the tab of the source you use.

## Class Metadata

### Normalize As Array

Normalizes objects of the class into an associative array (`true`) or into
`stdClass` (`false`). Overrides the
[configuration option](mapper-configuration.md#object-as-array).

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\NormalizeAsArray;

#[NormalizeAsArray(false)]
final class User
{
    public string $name = 'Ada';
}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<warning>
Not supported by reflection
</warning>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\User::class => [
        'normalize_as_array' => false,
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\User": {
        "normalize_as_array": false
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\User:
    normalize_as_array: false
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\User:
    normalize_as_array: false
```

</tab>
</tabs>

```php
$mapper->normalize(new User()); // (object) ['name' => 'Ada']
```

### Discriminator Map

Selects the class of the data by the value of a field. Is declared on a base
class or an interface.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\DiscriminatorMap;

#[DiscriminatorMap(field: 'type', map: [
    'circle' => Circle::class,
    'square' => Square::class,
])]
abstract class Shape {}

final class Circle extends Shape
{
    public float $radius;
}

final class Square extends Shape
{
    public float $side;
}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<warning>
Not supported by reflection
</warning>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\Shape::class => [
        'discriminator' => [
            'field' => 'type',
            'map' => [
                'circle' => App\Circle::class,
                'square' => App\Square::class,
            ],
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\Shape": {
        "discriminator": {
            "field": "type",
            "map": {
                "circle": "App\\Circle",
                "square": "App\\Square"
            }
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\Shape:
    discriminator:
        field: type
        map:
            circle: App\Circle
            square: App\Square
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\Shape:
    discriminator:
        field: type
        map:
            circle: App\Circle
            square: App\Square
```

</tab>
</tabs>

```php
$circle = $mapper->denormalize(['type' => 'circle', 'radius' => 2], Shape::class); // Circle { radius: 2.0 }
$mapper->denormalize(['type' => 'oval'], Shape::class);                            // throws InvalidValueException: $.type: Value must be of type "'circle'|'square'", but "oval" given

$mapper->normalize($circle, Shape::class);                                         // ['type' => 'circle', 'radius' => 2.0]
```

<note>
On normalization the tag is written as the first field. Pass the base type
to <code>normalize()</code>: without it the object is normalized by its own
class, without the tag.
</note>

#### Fallback Type

The `otherwise` type is used when the tag is missing or unknown.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\DiscriminatorMap;

#[DiscriminatorMap(field: 'type', map: [
    'circle' => Circle::class,
], otherwise: UnknownShape::class)]
abstract class Shape {}

final class Circle extends Shape
{
    public float $radius;
}

final class UnknownShape extends Shape {}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<warning>
Not supported by reflection
</warning>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\Shape::class => [
        'discriminator' => [
            'field' => 'type',
            'map' => [
                'circle' => App\Circle::class,
            ],
            'otherwise' => App\UnknownShape::class,
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\Shape": {
        "discriminator": {
            "field": "type",
            "map": {
                "circle": "App\\Circle"
            },
            "otherwise": "App\\UnknownShape"
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\Shape:
    discriminator:
        field: type
        map:
            circle: App\Circle
        otherwise: App\UnknownShape
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\Shape:
    discriminator:
        field: type
        map:
            circle: App\Circle
        otherwise: App\UnknownShape
```

</tab>
</tabs>

```php
$mapper->denormalize(['type' => 'oval'], Shape::class); // UnknownShape {}
```

### Naming Strategy

Renames all properties of the class, see
[naming strategies](mapper-naming.md).

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\NamingStrategy;

#[NamingStrategy('snake_case')]
final class User
{
    public string $firstName = 'Ada';
}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<warning>
Not supported by reflection
</warning>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\User::class => [
        'naming_strategy' => 'snake_case',
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\User": {
        "naming_strategy": "snake_case"
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\User:
    naming_strategy: snake_case
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\User:
    naming_strategy: snake_case
```

</tab>
</tabs>

```php
$mapper->normalize(new User()); // ['first_name' => 'Ada']
```

### Type Error Message

Replaces the message of the error when the value is not an object of the
class. Placeholders: `{{value}}`, `{{expected}}`.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\OnTypeError;

#[OnTypeError('A user is expected, {{value}} given')]
final class User
{
    public string $name;
}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<warning>
Not supported by reflection
</warning>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\User::class => [
        'type_error_message' => 'A user is expected, {{value}} given',
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\User": {
        "type_error_message": "A user is expected, {{value}} given"
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\User:
    type_error_message: 'A user is expected, {{value}} given'
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\User:
    type_error_message: 'A user is expected, {{value}} given'
```

</tab>
</tabs>

```php
$mapper->denormalize(42, User::class); // throws InvalidValueException: $: A user is expected, 42 given
```

## Enum Metadata

### Value Aliases

Other values accepted for a case of a backed enum, such as old values.
Normalization always gives the value of the case.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\MapAlias;

enum Status: string
{
    #[MapAlias('enabled', 'on')]
    case Active = 'active';

    case Blocked = 'blocked';
}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<warning>
Not supported by reflection
</warning>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\Status::class => [
        'aliases' => [
            'enabled' => 'active',
            'on' => 'active',
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\Status": {
        "aliases": {
            "enabled": "active",
            "on": "active"
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\Status:
    aliases:
        enabled: active
        'on': active
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\Status:
    aliases:
        enabled: active
        on: active
```

</tab>
</tabs>

```php
$mapper->denormalize('on', Status::class); // Status::Active
$mapper->normalize(Status::Active);        // 'active'
```

## Property Metadata

### Type

The type of the property: any [supported type](mapper-types.md). When it is
not specified, the native type of the property is used.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\MapType;

final class User
{
    #[MapType('list<non-empty-string>')]
    public array $tags = [];
}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

```php
final class User
{
    /**
     * @var list<non-empty-string>
     */
    public array $tags = [];
}
```

</tab>
<tab title="Reflection" group-key="reflection">

```php
final class User
{
    public array $tags = [];
}
```

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\User::class => [
        'properties' => [
            'tags' => 'list<non-empty-string>',
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\User": {
        "properties": {
            "tags": "list<non-empty-string>"
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\User:
    properties:
        tags: list<non-empty-string>
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\User:
    properties:
        tags: list<non-empty-string>
```

</tab>
</tabs>

```php
$mapper->denormalize(['tags' => ['php', 'mapper']], User::class); // User { tags: ['php', 'mapper'] }
$mapper->denormalize(['tags' => ['php', '']], User::class);       // throws InvalidValueException: $.tags[1]: Value must be of type "non-empty-string" (non-empty)
```

<tip>
Names of classes are resolved by the <code>use</code> statements of the
file, as in PHP: <code>#[MapType('list<Address>')]</code>.
</tip>

#### Read and Write Types

Different types for normalization (read) and denormalization (write).

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

<warning>
Not supported by attributes, except <code>#[MapType]</code> on the <code>get</code> and <code>set</code> hooks of a PHP 8.4 virtual property
</warning>

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc, except <code>@var</code> on the <code>get</code> and <code>set</code> hooks of a PHP 8.4 virtual property
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<warning>
Not supported by reflection, except the types of the <code>get</code> and <code>set</code> hooks of a PHP 8.4 virtual property
</warning>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\User::class => [
        'properties' => [
            'id' => [
                'read_type' => 'int',
                'write_type' => 'positive-int',
            ],
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\User": {
        "properties": {
            "id": {
                "read_type": "int",
                "write_type": "positive-int"
            }
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\User:
    properties:
        id:
            read_type: int
            write_type: positive-int
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\User:
    properties:
        id:
            read_type: int
            write_type: positive-int
```

</tab>
</tabs>

### Strict Types

Disables [type coercion](mapper-coercion.md) for the property, or enables it
with `false`. Overrides the
[configuration option](mapper-configuration.md#strict-types).

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\MapType;

final class User
{
    #[MapType('int', strict: true)]
    public int $id;
}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<warning>
Not supported by reflection
</warning>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\User::class => [
        'properties' => [
            'id' => [
                'type' => 'int',
                'strict' => true,
            ],
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\User": {
        "properties": {
            "id": {
                "type": "int",
                "strict": true
            }
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\User:
    properties:
        id:
            type: int
            strict: true
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\User:
    properties:
        id:
            type: int
            strict: true
```

</tab>
</tabs>

```php
$mapper->denormalize(['id' => 42], User::class);   // User { id: 42 }
$mapper->denormalize(['id' => '42'], User::class); // throws InvalidValueException: $.id: Value must be of type "int", but "42" given
```

### Field Name

The name of the field in the data, in both directions.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\MapName;

final class User
{
    #[MapName('e_mail')]
    public string $email = 'ada@example.com';
}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<warning>
Not supported by reflection
</warning>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\User::class => [
        'properties' => [
            'email' => [
                'name' => 'e_mail',
            ],
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\User": {
        "properties": {
            "email": {
                "name": "e_mail"
            }
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\User:
    properties:
        email:
            name: e_mail
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\User:
    properties:
        email:
            name: e_mail
```

</tab>
</tabs>

```php
$mapper->normalize(new User());                                     // ['e_mail' => 'ada@example.com']
$mapper->denormalize(['e_mail' => 'bob@example.com'], User::class); // User { email: 'bob@example.com' }
```

### Default Value

The value of a missing field. A property without a default value is
required.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

<warning>
Not supported by attributes
</warning>

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

```php
final class User
{
    public string $role = 'user';
}
```

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\User::class => [
        'properties' => [
            'role' => [
                'default' => 'user',
            ],
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\User": {
        "properties": {
            "role": {
                "default": "user"
            }
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\User:
    properties:
        role:
            default: user
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\User:
    properties:
        role:
            default: user
```

</tab>
</tabs>

```php
$mapper->denormalize(['name' => 'Ada'], User::class);   // User { name: 'Ada', role: 'user' }
$mapper->denormalize(['role' => 'admin'], User::class); // throws InvalidValueException: $.name: Required field "name" is missing
```

<note>
A promoted property takes the default value of its constructor parameter.
<code>null</code> is a value, not a missing field: it must match the type.
</note>

### Type Error Message

Replaces the message of the error of the property value. Placeholders:
`{{value}}`, `{{expected}}`.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\OnTypeError;

final class User
{
    #[OnTypeError('The age must be a number, {{value}} given')]
    public int $age;
}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<warning>
Not supported by reflection
</warning>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\User::class => [
        'properties' => [
            'age' => [
                'type_error_message' => 'The age must be a number, {{value}} given',
            ],
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\User": {
        "properties": {
            "age": {
                "type_error_message": "The age must be a number, {{value}} given"
            }
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\User:
    properties:
        age:
            type_error_message: 'The age must be a number, {{value}} given'
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\User:
    properties:
        age:
            type_error_message: 'The age must be a number, {{value}} given'
```

</tab>
</tabs>

```php
$mapper->denormalize(['age' => 'old'], User::class); // throws InvalidValueException: $.age: The age must be a number, "old" given
```

### Undefined Error Message

Replaces the message of the error of a missing field. Placeholder:
`{{field}}`.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\OnUndefinedError;

final class User
{
    #[OnUndefinedError('The {{field}} is required')]
    public string $email;
}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<warning>
Not supported by reflection
</warning>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\User::class => [
        'properties' => [
            'email' => [
                'undefined_error_message' => 'The {{field}} is required',
            ],
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\User": {
        "properties": {
            "email": {
                "undefined_error_message": "The {{field}} is required"
            }
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\User:
    properties:
        email:
            undefined_error_message: 'The {{field}} is required'
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\User:
    properties:
        email:
            undefined_error_message: 'The {{field}} is required'
```

</tab>
</tabs>

```php
$mapper->denormalize([], User::class); // throws InvalidValueException: $.email: The email is required
```

### Skip

Leaves the property out of the normalized data under a condition. Several
conditions can be combined: the property is skipped when any of them
matches.

#### When Null

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\SkipWhenNull;

final class User
{
    #[SkipWhenNull]
    public ?string $phone = null;
}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<warning>
Not supported by reflection
</warning>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\User::class => [
        'properties' => [
            'phone' => [
                'skip' => 'null',
            ],
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\User": {
        "properties": {
            "phone": {
                "skip": "null"
            }
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\User:
    properties:
        phone:
            skip: 'null'
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\User:
    properties:
        phone:
            skip: 'null'
```

</tab>
</tabs>

```php
$mapper->normalize(new User()); // []
```

<warning>
In YAML the condition is the string <code>'null'</code> in quotes: an
unquoted <code>null</code> is not accepted.
</warning>

#### When Empty

Skips `null`, `''`, `[]`, `0`, `'0'` and `false`.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\SkipWhenEmpty;

final class User
{
    #[SkipWhenEmpty]
    public array $tags = [];
}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<warning>
Not supported by reflection
</warning>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\User::class => [
        'properties' => [
            'tags' => [
                'skip' => 'empty',
            ],
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\User": {
        "properties": {
            "tags": {
                "skip": "empty"
            }
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\User:
    properties:
        tags:
            skip: empty
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\User:
    properties:
        tags:
            skip: empty
```

</tab>
</tabs>

```php
$mapper->normalize(new User()); // []
```

#### Expression

An expression of the
[Symfony Expression Language](https://symfony.com/doc/current/components/expression_language.html),
`this` is the object. Requires `symfony/expression-language`.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\SkipWhen;

final class User
{
    public bool $isGuest = true;

    #[SkipWhen('this.isGuest')]
    public string $email = 'guest@example.com';
}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
Not supported by PHPDoc
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<warning>
Not supported by reflection
</warning>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\User::class => [
        'properties' => [
            'email' => [
                'skip' => 'this.isGuest',
            ],
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\User": {
        "properties": {
            "email": {
                "skip": "this.isGuest"
            }
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\User:
    properties:
        email:
            skip: this.isGuest
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\User:
    properties:
        email:
            skip: this.isGuest
```

</tab>
</tabs>

```php
$mapper->normalize(new User()); // ['isGuest' => true]
```

<warning>
Functions such as <code>constant()</code> are not supported in expressions:
use properties, methods of the object and operators.
</warning>
