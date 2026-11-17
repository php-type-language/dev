# Skipping Properties

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

Leaves a property out of the normalized data under a condition. Several
conditions can be combined: the property is skipped when any of them matches.

Every option is shown in each [metadata source](mapper-metadata.md): choose
the tab of the source you use. When a source cannot express an option, its
tab says what is used instead.

## When Null

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
There is no PHPDoc tag for this option.
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<note>
PHP has no such declaration: the property is always normalized.
</note>

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

## When Empty

Skips `null`, `false`, `0`, `0.0`, `''`, `'0'` and `[]`.

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
There is no PHPDoc tag for this option.
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<note>
PHP has no such declaration: the property is always normalized.
</note>

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

## Expression

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
There is no PHPDoc tag for this option.
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<note>
PHP has no such declaration: the property is always normalized.
</note>

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
