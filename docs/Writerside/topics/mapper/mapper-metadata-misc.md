# Miscellaneous

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

Other options of classes, properties and enums.

Every option is shown in each [metadata source](mapper-metadata.md): choose
the tab of the source you use. When a source cannot express an option, its
tab says what is used instead.

## Normalize As Array

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
There is no PHPDoc tag for this option.
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<note>
PHP has no such declaration: the <a href="mapper-configuration.md#object-as-array">configuration option</a> is used.
</note>

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

## Default Value

The value of a missing field. A property without a default value is
required.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

<warning>
There is no attribute for a default value: it is written in PHP, see the Reflection tab.
</warning>

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
There is no PHPDoc tag for a default value: it is written in PHP, see the Reflection tab.
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

```php
final class User
{
    public string $role = 'user';

    public function __construct(
        public string $lang = 'en',
    ) {}
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
$mapper->denormalize(['name' => 'Ada'], User::class);
// User { name: 'Ada', role: 'user' }

$mapper->denormalize(['role' => 'admin'], User::class);
// throws InvalidValueException: $.name: Required field "name" is missing
```

<note>
A promoted property takes the default value of its constructor parameter.
<code>null</code> is a value, not a missing field: it must match the type.
</note>

## Value Aliases

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
There is no PHPDoc tag for this option.
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<note>
Only the values of the cases are accepted.
</note>

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
