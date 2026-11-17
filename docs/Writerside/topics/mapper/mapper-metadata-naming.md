# Naming

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

The names of the fields of the data: of a single property and by a rule for all
properties.

Every option is shown in each [metadata source](mapper-metadata.md): choose
the tab of the source you use. When a source cannot express an option, its
tab says what is used instead.

## Field Name

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
There is no PHPDoc tag for this option.
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

```php
final class User
{
    // the field "email"
    public string $email;
}
```

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
$mapper->normalize(new User());
// ['e_mail' => 'ada@example.com']

$mapper->denormalize(['e_mail' => 'bob@example.com'], User::class);
// User { email: 'bob@example.com' }
```

## Naming Strategy

Renames the fields of all properties of the class by a rule, in both
directions.

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
There is no PHPDoc tag for this option.
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<note>
PHP has no such declaration: the <a href="#for-the-whole-mapper">strategy of the mapper</a> is used, without it the names of the properties are kept.
</note>

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
$mapper->normalize(new User());
// ['first_name' => 'Ada']

$mapper->denormalize(['first_name' => 'Bob'], User::class);
// User { firstName: 'Bob' }
```

The strategy is a name of the table below, a class name of a strategy or,
in attributes, an instance of a strategy.

| Name                   | Class                              | `userId`  |
|------------------------|------------------------------------|-----------|
| `snake_case`           | `SnakeCaseNamingStrategy`          | `user_id` |
| `camelCase`            | `CamelCaseNamingStrategy`          | `userId`  |
| `PascalCase`           | `PascalCaseNamingStrategy`         | `UserId`  |
| `kebab-case`           | `KebabCaseNamingStrategy`          | `user-id` |
| `SCREAMING_SNAKE_CASE` | `ScreamingSnakeCaseNamingStrategy` | `USER_ID` |

The classes are in the `TypeLang\Mapper\Naming` namespace.

```php
$snake->rename('firstName');  // 'first_name'
$snake->rename('userID');     // 'user_id'
$snake->rename('HTTPServer'); // 'http_server'
$snake->rename('address2');   // 'address2'
$camel->rename('created_at'); // 'createdAt'
```

A [field name](#field-name) of a property always wins over the strategy.

### For the Whole Mapper

A strategy of the mapper is used for the classes without their own one.

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;
use TypeLang\Mapper\Naming\SnakeCaseNamingStrategy;

$mapper = new Mapper(
    metadata: MetadataProviderFactory::create(
        strategy: new SnakeCaseNamingStrategy(),
    ),
);

$mapper->normalize(new User()); // ['first_name' => 'Ada']
```

### Custom Strategy

```php
use TypeLang\Mapper\Naming\NamingStrategyInterface;

final class UpperNamingStrategy implements NamingStrategyInterface
{
    public function rename(string $name): string
    {
        return \strtoupper($name);
    }
}

#[NamingStrategy(new UpperNamingStrategy())]
final class User
{
    public string $name = 'Ada';
}

$mapper->normalize(new User()); // ['NAME' => 'Ada']
```

In config files, a custom strategy is given by its class name. Its
constructor must have no required arguments.
