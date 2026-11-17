# Property Types

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

The type of a property and its strictness.

Every option is shown in each [metadata source](mapper-metadata.md): choose
the tab of the source you use. When a source cannot express an option, its
tab says what is used instead.

## Type

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

    /**
     * @param list<non-empty-string> $roles
     */
    public function __construct(
        public array $roles = [],
    ) {}
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
$mapper->denormalize(['tags' => ['php', 'mapper']], User::class);
// User { tags: ['php', 'mapper'] }

$mapper->denormalize(['tags' => ['php', '']], User::class);
// throws InvalidValueException: $.tags[1]: Value must be of type
// "non-empty-string" (non-empty)
```

<tip>
In attributes and PHPDoc names of classes are resolved by the
<code>use</code> statements of the file, as in PHP:
<code>#[MapType('list<Address>')]</code>. In config files they are fully
qualified.
</tip>

### Read and Write Types

Different types for normalization (read) and denormalization (write), given
on the `get` and `set` hooks of a property (PHP 8.4):

- attributes: a type on the property itself sets both types;
- PHPDoc: `@var` sets both types, on a property with a `set` hook only the
  read type;
- reflection: the read type is the declared type of the property, the write
  type is the parameter type of the `set` hook.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\MapType;

final class User
{
    private string $value = '';

    public mixed $id {
        #[MapType('int')]
        get => (int) $this->value;
        #[MapType('positive-int')]
        set(mixed $id) {
            $this->value = (string) $id;
        }
    }
}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

```php
final class User
{
    private string $value = '';

    public mixed $id {
        /** @return int */
        get => (int) $this->value;
        /** @param positive-int $id */
        set(mixed $id) {
            $this->value = (string) $id;
        }
    }
}
```

</tab>
<tab title="Reflection" group-key="reflection">

```php
final class User
{
    private string $value = '';

    public int $id {
        get => (int) $this->value;
        set(int|string $id) {
            $this->value = (string) $id;
        }
    }
}
```

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

```php
$user = $mapper->denormalize(['id' => '42'], User::class);
$user->id;                 // 42
$mapper->normalize($user); // ['id' => 42]
$mapper->denormalize(['id' => -1], User::class);
// throws InvalidValueException: $.id: Value must be of type "positive-int"
// (>= 1)
```

<warning>
The hooks are called only on a virtual property, such as above. A property
with its own value is written and read directly, bypassing its hooks.
</warning>

## Strict Types

Disables [type coercion](mapper-coercion.md) for the value of the property
and its nested values, or enables it with `false`. Overrides the
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
There is no PHPDoc tag for this option.
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<note>
PHP has no such declaration: the <a href="mapper-configuration.md#strict-types">configuration option</a> is used.
</note>

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
$mapper->denormalize(['id' => 42], User::class); // User { id: 42 }
$mapper->denormalize(['id' => '42'], User::class);
// throws InvalidValueException: $.id: Value must be of type "int", but "42"
// given
```
