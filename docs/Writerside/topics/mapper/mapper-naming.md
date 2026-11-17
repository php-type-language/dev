# Naming Strategies

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

A naming strategy renames the fields of the data by a rule, such as
`snake_case`.

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

## For a Class

```php
#[NamingStrategy('snake_case')]
final class User
{
    public string $firstName = 'Ada';
}

$mapper->normalize(new User()); // ['first_name' => 'Ada']
```

The attribute takes a name of the table above, a class name or an instance of
a strategy. Other sources: see
[writing metadata](mapper-metadata-options.md#naming-strategy).

## For the Mapper

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

Priority, from the highest: the
[field name](mapper-metadata-options.md#field-name) of a property, the
strategy of the class, the strategy of the mapper.

## Custom Strategy

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
