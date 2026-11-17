# Naming Strategies

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

By default a property is mapped to the field with exactly its name:
`$streetName` reads and writes `streetName`. When the data follows another
convention, such as `street_name` in a JSON API, a **naming strategy**
converts the property names by a rule, so the classes keep PHP naming and no
`#[MapName]` is needed on every property.

A strategy can be set for one class or for the whole mapper. The converted
name is used in both directions and in error paths.

## Built-in Strategies

| Name | Class | Example |
|---|---|---|
| `snake_case` | `SnakeCaseNamingStrategy` | `user_id` |
| `camelCase` | `CamelCaseNamingStrategy` | `userId` |
| `PascalCase` | `PascalCaseNamingStrategy` | `UserId` |
| `kebab-case` | `KebabCaseNamingStrategy` | `user-id` |
| `SCREAMING_SNAKE_CASE` | `ScreamingSnakeCaseNamingStrategy` | `USER_ID` |

The classes live in the `TypeLang\Mapper\Naming` namespace. Each one splits
the property name into words and joins them in its own style:

| Property | `snake_case` | `camelCase` | `PascalCase` | `kebab-case` | `SCREAMING_SNAKE_CASE` |
|---|---|---|---|---|---|
| `firstName` | `first_name` | `firstName` | `FirstName` | `first-name` | `FIRST_NAME` |
| `userID` | `user_id` | `userId` | `UserId` | `user-id` | `USER_ID` |
| `address2Line` | `address2_line` | `address2Line` | `Address2Line` | `address2-line` | `ADDRESS2_LINE` |
| `HTTPResponse` | `http_response` | `httpResponse` | `HttpResponse` | `http-response` | `HTTP_RESPONSE` |
| `imageURLs` | `image_urls` | `imageUrls` | `ImageUrls` | `image-urls` | `IMAGE_URLS` |
| `utf8Text` | `utf8_text` | `utf8Text` | `Utf8Text` | `utf8-text` | `UTF8_TEXT` |
| `created_at` | `created_at` | `createdAt` | `CreatedAt` | `created-at` | `CREATED_AT` |
| `_internalId` | `_internal_id` | `_internalId` | `_InternalId` | `_internal-id` | `_INTERNAL_ID` |

The words are split as follows:

- a new word starts at an uppercase letter and at a separator (`_`, `-`, any
  other character that is not a letter or a digit);
- a run of uppercase letters is one word, an acronym: `HTTP`, `ID`; a
  trailing `s` stays with it (`URLs`);
- digits stay with the word before them: `address2`, `utf8`;
- separators before the first word and after the last one are kept as they
  are (`_internalId`).

Acronyms are not preserved: `userID` becomes `userId` in `camelCase`, so a
strategy may change a name that already looks like its style. Only ASCII
letters change case.

## For One Class

The `#[NamingStrategy]` attribute sets the strategy of one class. It takes
the name of a built-in strategy, the class name of a strategy, or a strategy
object:

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MapName;
use TypeLang\Mapper\Mapping\NamingStrategy;
use TypeLang\Mapper\Naming\KebabCaseNamingStrategy;

#[NamingStrategy('SCREAMING_SNAKE_CASE')]
final class EnvConfig
{
    public string $databaseUrl = 'sqlite::memory:';
}

#[NamingStrategy(KebabCaseNamingStrategy::class)]
final class Headers
{
    public string $contentType = 'text/plain';
}

#[NamingStrategy(new PrefixNamingStrategy('user_'))]
final class Account
{
    public int $accountId = 1;

    #[MapName('email')]
    public string $emailAddress = 'ada@example.com';
}

$mapper = new Mapper();

echo \json_encode($mapper->normalize(new EnvConfig())), "\n";
echo \json_encode($mapper->normalize(new Headers())), "\n";
echo \json_encode($mapper->normalize(new Account())), "\n";
```

```
{"DATABASE_URL":"sqlite::memory:"}
{"content-type":"text\/plain"}
{"user_account_id":1,"email":"ada@example.com"}
```

A class name must point to a strategy whose constructor has no required
arguments; a strategy that needs arguments is passed as an object
(`PrefixNamingStrategy` is the custom strategy written
[below](#writing-a-strategy)). An unknown name makes the class unusable:

```
Type "Point" cannot be built: Cannot read metadata of "Point":
  - Point: naming strategy "snakeCase" is neither a built-in one nor an instantiable class of "TypeLang\Mapper\Naming\NamingStrategyInterface" in /path/to/Point.php
```

The strategy applies to the properties declared in the class and to the ones
it inherits. The attribute itself is not inherited, like any PHP attribute:
a child class of a `snake_case` class keeps the original names (or the mapper
strategy) unless it declares `#[NamingStrategy]` too. In a configuration file the same setting is the
`naming_strategy` key of the class, see [Metadata](mapper-metadata.md).

## For the Whole Mapper

A strategy for every class is passed to `MetadataProviderFactory::create()`,
and the resulting provider to the mapper (`Headers` is the class from the
previous example):

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;
use TypeLang\Mapper\Naming\SnakeCaseNamingStrategy;

final class Address
{
    public string $streetName = 'Baker Street';

    public string $postCode = 'NW1 6XE';
}

$mapper = new Mapper(metadata: MetadataProviderFactory::create(
    strategy: new SnakeCaseNamingStrategy(),
));

echo \json_encode($mapper->normalize(new Address())), "\n";
echo \json_encode($mapper->normalize(new Headers())), "\n";

$address = $mapper->denormalize(['street_name' => 'Abbey Road', 'post_code' => 'NW8 9AY'], Address::class);

echo $address->streetName, "\n";
```

```
{"street_name":"Baker Street","post_code":"NW1 6XE"}
{"content-type":"text\/plain"}
Abbey Road
```

`Headers` keeps its own `kebab-case`: a class strategy wins over the mapper
strategy.

## Priority

For each property the name is chosen in this order:

1. an explicit name: `#[MapName]`, or the `name` key in a configuration
   file;
2. the strategy of the class;
3. the strategy of the mapper;
4. the property name as is.

`$emailAddress` in `Account` above is written as `email`, not
`user_email_address`.

If two properties of a class end up with the same name, the class cannot be
used:

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\NamingStrategy;

#[NamingStrategy('snake_case')]
final class Row
{
    public int $userId = 1;

    public int $user_id = 2;
}

try {
    (new Mapper())->normalize(new Row());
} catch (\Exception $e) {
    echo $e->getMessage(), "\n";
}
```

```
Type "Row" cannot be built: Cannot read metadata of "Row":
  - Row: properties "$userId", "$user_id" have the same external name "user_id" in /path/to/Row.php
```

## Writing a Strategy

A strategy implements `NamingStrategyInterface`: one method that receives
the property name and returns the field name, which must not be empty.

```php
use TypeLang\Mapper\Naming\NamingStrategyInterface;
use TypeLang\Mapper\Naming\SnakeCaseNamingStrategy;

final class PrefixNamingStrategy implements NamingStrategyInterface
{
    public function __construct(
        private readonly string $prefix,
        private readonly NamingStrategyInterface $inner = new SnakeCaseNamingStrategy(),
    ) {}

    public function rename(string $name): string
    {
        return $this->prefix . $this->inner->rename($name);
    }
}
```

It is used like a built-in one: as an object in `#[NamingStrategy(...)]` or
in `MetadataProviderFactory::create(strategy: ...)`, or by class name when
its constructor has no required arguments.

<note>
Names are computed once, when the metadata of a class is read, and are
cached with it. A strategy must return the same name for the same input.
</note>
