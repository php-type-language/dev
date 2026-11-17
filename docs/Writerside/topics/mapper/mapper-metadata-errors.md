# Error Messages

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

Custom messages of the errors of a class and of its properties. Other details
of errors are described in [errors](mapper-errors.md).

Every option is shown in each [metadata source](mapper-metadata.md): choose
the tab of the source you use. When a source cannot express an option, its
tab says what is used instead.

## Class Type Error

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
There is no PHPDoc tag for this option.
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<note>
The default message is used: <code>Value must be of type "App\User", but 42 given</code>.
</note>

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
$mapper->denormalize(42, User::class);
// throws InvalidValueException: $: A user is expected, 42 given
```

## Property Type Error

Replaces the message of the error of the property value. Errors of its
nested values keep their own messages. Placeholders: `{{value}}`,
`{{expected}}`.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\OnTypeError;

final class User
{
    #[OnTypeError('Age must be a number, {{value}} given')]
    public int $age;
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
The default message is used: <code>Value must be of type "int", but "old" given</code>.
</note>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\User::class => [
        'properties' => [
            'age' => [
                'type_error_message' => 'Age must be a number, {{value}} given',
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
                "type_error_message": "Age must be a number, {{value}} given"
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
            type_error_message: 'Age must be a number, {{value}} given'
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\User:
    properties:
        age:
            type_error_message: 'Age must be a number, {{value}} given'
```

</tab>
</tabs>

```php
$mapper->denormalize(['age' => 'old'], User::class);
// throws InvalidValueException: $.age: Age must be a number, "old" given
```

## Missing Field Error

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
There is no PHPDoc tag for this option.
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<note>
The default message is used: <code>Required field "email" is missing</code>.
</note>

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
$mapper->denormalize([], User::class);
// throws InvalidValueException: $.email: The email is required
```
