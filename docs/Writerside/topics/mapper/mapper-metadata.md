# Metadata

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

Before the mapper converts an object, it needs a description of its class:
which properties take part, the name of each one in the data, the types it
is read and written with, default values, skip conditions, error messages,
and for the class itself the discriminator, the naming strategy and the form
of the normalized object. This description is the **metadata** of the class.

The metadata is gathered from several sources (attributes, PHPDoc, config
files, the declaration of the class itself) once per class, then kept in
memory and, optionally, in a PSR cache. This page describes the sources, the
order in which they override each other, the format of the config files and
the cache.

## Sources and Priority

`new Mapper()` without the `metadata` argument creates the default stack with
`MetadataProviderFactory::create()`. From the outside in it is:

1. an in-memory cache, which keeps the metadata of each class for the
   lifetime of the process;
2. a PSR-6 or PSR-16 cache, if one is given (see [Caching](#caching));
3. final checks: an unparsable type that is still in use, and two properties
   with the same name in the data, are reported here;
4. the chain of readers, from the highest priority to the lowest one:

| Reader                         | Reads                                                                                         |
|--------------------------------|-----------------------------------------------------------------------------------------------|
| naming strategy                | names the properties no other source gave an explicit name (see [](mapper-naming.md))         |
| readers you pass in `readers:` | config arrays and files (see [Config Files](#config-files)) or [your own](#custom-readers)     |
| attributes                     | `#[MapName]`, `#[MapType]` and the other [attributes](#attributes)                            |
| PHPDoc                         | `@type-lang-var` and friends, then `@var`, `@param`, `@return` (if `type-lang/phpdoc` is installed) |
| reflection                     | the properties of the class, their native types and default values                           |

The readers run from the bottom up. Reflection is the base: it is the only
reader that adds properties, by default every public non-static one. Every
reader above it receives the metadata read so far and overrides the values it
defines, value by value, for the properties that are already there. A reader
that says nothing about a value leaves it as it is. The naming strategy is
the exception: it does not override anything and only fills in the names no
reader below it has set.

So a type from a config file beats `#[MapType]`, which beats `@var`, which
beats the native type, while a `#[MapName]` on the same property still
applies if the config does not set `name`:

```php
namespace App;

use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MapName;
use TypeLang\Mapper\Mapping\MapType;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;
use TypeLang\Mapper\Mapping\Reader\ArrayMetadataReader;

final class Address
{
    public string $city;
}

final class Order
{
    /** @var list<Address> */
    public array $addresses;

    /** @var int */
    #[MapType('positive-int')]
    public int $quantity;

    #[MapName('note_text')]
    public string $note;
}

$config = new ArrayMetadataReader([
    Order::class => [
        'properties' => [
            'note' => ['name' => 'comment'],
        ],
    ],
]);

$mapper = new Mapper(metadata: MetadataProviderFactory::create(readers: [$config]));

var_dump($mapper->denormalize([
    'addresses' => [['city' => 'Paris']],
    'quantity' => 2,
    'comment' => 'Leave at the door',
], Order::class));

try {
    $mapper->denormalize(['addresses' => [], 'quantity' => 0, 'comment' => ''], Order::class);
} catch (\Throwable $e) {
    echo $e->getMessage(), "\n";
}
```

```
object(App\Order)#920 (3) {
  ["addresses"]=>
  array(1) {
    [0]=>
    object(App\Address)#919 (1) {
      ["city"]=>
      string(5) "Paris"
    }
  }
  ["quantity"]=>
  int(2)
  ["note"]=>
  string(17) "Leave at the door"
}
$.quantity: Value must be of type "positive-int" (>= 1)
```

`addresses` takes its type from `@var`, `quantity` from the attribute over
`@var int`, and `note` its name from the config over `#[MapName]`.

`MetadataProviderFactory::create()` takes these arguments:

| Argument    | Default                    | Meaning                                                                              |
|-------------|----------------------------|--------------------------------------------------------------------------------------|
| `readers`   | `[]`                       | readers above the default ones, from the highest priority to the lowest one          |
| `strategy`  | `null`                     | naming strategy of the classes without their own, names are kept as is on `null`     |
| `cache`     | `null`                     | a PSR-6 `CacheItemPoolInterface` or a PSR-16 `CacheInterface`                        |
| `prefix`    | `'tlm_'`                   | prefix of the cache keys                                                             |
| `parser`    | `new TypeParser()`         | parser of the types in attributes and PHPDoc                                         |
| `selector`  | `new PublicPropertySelector()` | `PropertySelectorInterface` deciding which properties of a class are mapped     |

The result is passed to the mapper as `new Mapper(metadata: $provider)`.

<tip>
Names in <code>#[MapType]</code>, <code>#[DiscriminatorMap]</code> and PHPDoc
are resolved the way PHP resolves them in the file of the class: by its
<code>use</code> imports and its namespace. A simple name such as
<code>Address</code> becomes <code>App\Address</code> when such a class,
interface or enum exists, otherwise it stays as written, so mapper types
like <code>positive-int</code> are not affected. Names in config files are not
resolved: write classes fully qualified there.
</tip>

<warning>
The namespace check ignores the case of the name, as PHP does. Inside
<code>namespace App</code>, <code>#[MapType('money')]</code> means the class
<code>App\Money</code> if it exists, not a type registered as
<code>money</code>. Give your own types names that cannot be class names
(<code>money-string</code>), see <a href="mapper-extending.md">Extending</a>.
</warning>

## Attributes

All attributes live in the `TypeLang\Mapper\Mapping` namespace.

| Attribute                                  | Target         | Meaning                                                                                                                 |
|--------------------------------------------|----------------|-------------------------------------------------------------------------------------------------------------------------|
| `#[MapName('user_id')]`                    | property       | name of the property in the data, see [](mapper-classes.md#field-names)                                                 |
| `#[MapType('list<Address>', strict: true)]` | property       | type of both directions and optionally its strictness, see [](mapper-classes.md#property-types)                         |
| `#[MapType(...)]` on a `get` / `set` hook  | method         | type of reading / of writing only (PHP 8.4+), see [](mapper-classes.md#property-types)                                  |
| `#[SkipWhenNull]`                          | property       | left out of the normalized data when `null`, see [](mapper-classes.md#skipping-properties-on-normalization)             |
| `#[SkipWhenEmpty]`                         | property       | left out when `empty()`                                                                                                  |
| `#[SkipWhen('this.isPrivate()')]`          | property       | left out when the expression is true (requires `symfony/expression-language`), repeatable                              |
| `#[OnTypeError('...')]`                    | property, class | message of an invalid value, see [](mapper-errors.md#custom-messages)                                                  |
| `#[OnUndefinedError('...')]`               | property       | message of a missing field, see [](mapper-errors.md#custom-messages)                                                   |
| `#[NormalizeAsArray(false)]`               | class          | normalize the objects of the class to `\stdClass` (or to arrays with `true`), see [](mapper-classes.md#arrays-or-objects-on-normalization) |
| `#[NamingStrategy('snake_case')]`          | class          | naming strategy of the properties of the class, see [](mapper-naming.md)                                                |
| `#[DiscriminatorMap(field: 'type', map: [...])]` | class    | polymorphic class, see [](mapper-polymorphism.md)                                                                       |
| `#[MapAlias('on', 'enabled')]`             | enum case      | other values denormalized to this case of a backed enum, repeatable, see [](mapper-types.md)                            |

An attribute on a property that is not mapped (a private or a static one with
the default selector) is an error, not a silently ignored annotation. So is
an attribute with invalid arguments, even if a config file sets the same
value.

## PHPDoc

With the `type-lang/phpdoc` package installed, types are also read from
docblocks:

```shell
composer require type-lang/phpdoc
```

| Where                                   | Tags                                                                       |
|-----------------------------------------|----------------------------------------------------------------------------|
| docblock of the property                | `@var`, `@phpstan-var`, `@psalm-var`, `@phan-var`                          |
| docblock of the constructor, for a promoted property | `@param` and its prefixed forms                               |
| `get` hook (PHP 8.4+)                   | `@return` and its prefixed forms, the type of reading                      |
| `set` hook (PHP 8.4+)                   | `@param` and its prefixed forms, the type of writing                       |

The tags `@type-lang-var`, `@type-lang-param` and `@type-lang-return` are read
by a separate reader above the standard one, so they win over `@var` and the
rest. Use them when a type is meant for the mapper only, or when the
standard tag must stay readable by a tool that does not understand the
mapper's types.

```php
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

final class Post
{
    /**
     * @param list<non-empty-string> $tags
     */
    public function __construct(
        public readonly array $tags,
    ) {}

    /**
     * @var array<array-key, mixed>
     * @type-lang-var array<non-empty-string, int<0, max>>
     */
    public array $votes = [];

    /**
     * @phpstan-var positive-int
     */
    public int $rating = 1;
}

$mapper = new Mapper();

foreach ([
    ['tags' => ['php', '']],
    ['tags' => [], 'votes' => ['up' => -1]],
    ['tags' => [], 'rating' => 0],
] as $input) {
    try {
        $mapper->denormalize($input, Post::class);
    } catch (InvalidValueException $e) {
        echo $e->getMessage(), "\n";
    }
}
```

```
$.tags[1]: Value must be of type "non-empty-string" (non-empty)
$.votes.up: Value must be of type "int<0, max>" (>= 0)
$.rating: Value must be of type "positive-int" (>= 1)
```

Two things to keep in mind:

- A tag that cannot be parsed is skipped silently: a typo such as
  `@var list<Usr` leaves the native type `array` in place. The same text in
  `#[MapType]` is an error with its location.
- When one docblock has several of the standard tags (`@var array` and
  `@phpstan-var list<User>`), the first one wins. Use `@type-lang-var` to make
  the choice explicit.

## Config Files

Readers of config files keep the mapping out of the classes, for example for
classes you do not own. Each one reads the same format:

| Reader                   | Source                                             | Requires                |
|--------------------------|----------------------------------------------------|-------------------------|
| `ArrayMetadataReader`    | a PHP array, plus an optional file name for errors | -                       |
| `PhpFileMetadataReader`  | a PHP file that returns such an array              | -                       |
| `JsonFileMetadataReader` | a JSON file                                        | `ext-json`              |
| `YamlFileMetadataReader` | a YAML file                                        | `symfony/yaml`          |
| `NeonFileMetadataReader` | a NEON file                                        | `nette/neon`            |

A reader whose package is missing throws `MissingDependencyException` from
its constructor. The file is read and decoded on the first class the mapper
asks about, not in the constructor.

Readers are passed to the factory, the first one has the highest priority:

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;
use TypeLang\Mapper\Mapping\Reader\YamlFileMetadataReader;

$mapper = new Mapper(metadata: MetadataProviderFactory::create(
    readers: [
        new YamlFileMetadataReader(__DIR__ . '/config/mapping.local.yaml'),
        new YamlFileMetadataReader(__DIR__ . '/config/mapping.yaml'),
    ],
));
```

Several configs may describe the same class: as with any readers, the higher
one overrides the lower one value by value.

### Example

The same mapping in the four formats:

<tabs>
<tab title="YAML">

```yaml
App\User:
  naming_strategy: snake_case
  type_error_message: 'User must be an object'
  properties:
    id: positive-int
    email:
      name: e_mail
      type: non-empty-string
      undefined_error_message: 'Email is required'
    age:
      write_type: int<0, 150>
      strict: true
    tags:
      type: list<non-empty-string>
      default: []
    displayName:
      skip: 'null'
App\Status:
  aliases:
    enabled: active
    banned: blocked
```

</tab>
<tab title="JSON">

```json
{
    "App\\User": {
        "naming_strategy": "snake_case",
        "type_error_message": "User must be an object",
        "properties": {
            "id": "positive-int",
            "email": {
                "name": "e_mail",
                "type": "non-empty-string",
                "undefined_error_message": "Email is required"
            },
            "age": {
                "write_type": "int<0, 150>",
                "strict": true
            },
            "tags": {
                "type": "list<non-empty-string>",
                "default": []
            },
            "displayName": {
                "skip": "null"
            }
        }
    },
    "App\\Status": {
        "aliases": {
            "enabled": "active",
            "banned": "blocked"
        }
    }
}
```

</tab>
<tab title="NEON">

```
App\User:
	naming_strategy: snake_case
	type_error_message: 'User must be an object'
	properties:
		id: positive-int
		email:
			name: e_mail
			type: non-empty-string
			undefined_error_message: 'Email is required'
		age:
			write_type: 'int<0, 150>'
			strict: true
		tags:
			type: 'list<non-empty-string>'
			default: []
		displayName:
			skip: 'null'
App\Status:
	aliases:
		enabled: active
		banned: blocked
```

</tab>
<tab title="PHP">

```php
<?php

declare(strict_types=1);

use App\Status;
use App\User;

return [
    User::class => [
        'naming_strategy' => 'snake_case',
        'type_error_message' => 'User must be an object',
        'properties' => [
            'id' => 'positive-int',
            'email' => [
                'name' => 'e_mail',
                'type' => 'non-empty-string',
                'undefined_error_message' => 'Email is required',
            ],
            'age' => [
                'write_type' => 'int<0, 150>',
                'strict' => true,
            ],
            'tags' => [
                'type' => 'list<non-empty-string>',
                'default' => [],
            ],
            'displayName' => [
                'skip' => 'null',
            ],
        ],
    ],
    Status::class => [
        'aliases' => [
            'enabled' => 'active',
            'banned' => 'blocked',
        ],
    ],
];
```

</tab>
</tabs>

The classes carry no mapping at all:

```php
namespace App;

enum Status: string
{
    case Active = 'active';
    case Blocked = 'blocked';
}

final class User
{
    public int $id;

    public string $email;

    public int $age;

    public Status $status;

    public array $tags;

    public ?string $displayName = null;
}
```

```php
use App\User;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;
use TypeLang\Mapper\Mapping\Reader\YamlFileMetadataReader;

$mapper = new Mapper(metadata: MetadataProviderFactory::create(
    readers: [new YamlFileMetadataReader(__DIR__ . '/config/mapping.yaml')],
));

$user = $mapper->denormalize(
    ['id' => 1, 'e_mail' => 'jane@example.com', 'age' => 30, 'status' => 'enabled'],
    User::class,
);

echo \json_encode($mapper->normalize($user)), "\n";

foreach ([
    ['id' => 1, 'age' => 30, 'status' => 'active'],
    ['id' => 1, 'e_mail' => 'jane@example.com', 'age' => '30', 'status' => 'active'],
    'jane',
] as $data) {
    try {
        $mapper->denormalize($data, User::class);
    } catch (InvalidValueException $e) {
        echo $e->getMessage(), "\n";
    }
}
```

```
{"id":1,"e_mail":"jane@example.com","age":30,"status":"active","tags":[]}
$.e_mail: Email is required
$.age: Value must be of type "int", but "30" given
$: User must be an object
```

`PhpFileMetadataReader`, `JsonFileMetadataReader` and `NeonFileMetadataReader`
with the files of the other tabs give the same result. `tags` is missing in
the data and gets its default from the config, `displayName` is `null` and is
left out of the normalized data, `age` is strict and does not accept the
string `"30"`, and the old value `enabled` is read as `Status::Active`.

### Classes

The top-level keys are class names, fully qualified. The case does not matter
and the leading `\` is optional, but one class may appear only once in a
file. A class the mapper never asks about is not checked, so a typo in a class
name goes unnoticed.

| Key                  | Value                                                            | Same as                                                |
|----------------------|------------------------------------------------------------------|--------------------------------------------------------|
| `properties`         | map of property names to [property configs](#properties)         | -                                                      |
| `naming_strategy`    | a built-in strategy name or the class of a strategy without required constructor arguments | `#[NamingStrategy]`, see [](mapper-naming.md) |
| `normalize_as_array` | `true` or `false`                                                | `#[NormalizeAsArray]`                                  |
| `type_error_message` | message template of an invalid value of the class                | `#[OnTypeError]` on the class                          |
| `discriminator`      | map with `field`, `map` and optional `otherwise`                 | `#[DiscriminatorMap]`, see [](mapper-polymorphism.md)  |
| `aliases`            | map of other values to the values of the cases of a backed enum  | `#[MapAlias]`                                          |

The built-in strategy names are `camelCase`, `PascalCase`, `snake_case`,
`SCREAMING_SNAKE_CASE` and `kebab-case`. A discriminator in YAML:

```yaml
App\Shape:
  discriminator:
    field: kind
    map:
      circle: App\Circle
      square: App\Square
    otherwise: App\UnknownShape
```

### Properties

A property config is either a map of the keys below or a string, the short
form of `type`: `id: positive-int`. Only properties the reflection reader
has mapped can be configured: a key of a property that does not exist, or of
a private one, is an error.

| Key                       | Value                                                             | Same as                                   |
|---------------------------|-------------------------------------------------------------------|-------------------------------------------|
| `name`                    | name of the property in the data                                  | `#[MapName]`                              |
| `type`                    | type of both directions                                           | `#[MapType]`                              |
| `read_type`               | type of normalization only (reading the property)                 | `#[MapType]` on a `get` hook              |
| `write_type`              | type of denormalization only (writing the property)               | `#[MapType]` on a `set` hook              |
| `strict`                  | `true` or `false`, see below                                      | `strict:` of `#[MapType]`                 |
| `default`                 | value used when the field is missing                              | default value of the property             |
| `skip`                    | `'null'`, `'empty'`, an expression over `this`, or a list of them | `#[SkipWhenNull]`, `#[SkipWhenEmpty]`, `#[SkipWhen]` |
| `type_error_message`      | message template of an invalid value                              | `#[OnTypeError]`                          |
| `undefined_error_message` | message template of a missing field                               | `#[OnUndefinedError]`                     |

Details:

- `strict` together with `type`, `read_type` or `write_type` in the same
  config applies to those types only. Without a type of its own it makes the
  type set by a lower source (attribute, PHPDoc, native type) strict.
- `default` accepts `null`, a scalar, an enum case or an array of them. A
  case can only be written in a PHP config (`Status::Active`). A config
  cannot make a field required again once a lower source gave it a default.
- `skip: null` without quotes is the YAML/NEON `null` value, which is
  rejected: write `'null'` and `'empty'` as strings. Expressions require
  `symfony/expression-language`.

### Errors in Configs

All errors of a class are collected and reported together, each with the
file and the path of the key. Building the type of the class then fails with
a `TypeDefinitionException` that holds an `InvalidMetadataException` as its
previous exception:

```yaml
App\User:
  colour: red
  properties:
    id:
      type: list<int
    email:
      nmae: e_mail
    secret: string
```

```php
namespace App;

use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;
use TypeLang\Mapper\Mapping\Reader\YamlFileMetadataReader;

final class User
{
    public int $id;
    public string $email;
    private string $secret = '';
}

$mapper = new Mapper(metadata: MetadataProviderFactory::create(
    readers: [new YamlFileMetadataReader('config/broken.yaml')],
));

try {
    $mapper->denormalize(['id' => 1, 'email' => 'jane@example.com'], User::class);
} catch (\Throwable $e) {
    echo get_class($e), "\n", $e->getMessage(), "\n";
}
```

```
TypeLang\Mapper\Exception\TypeDefinitionException
Type "App\User" cannot be built: Cannot read metadata of "App\User":
  - App\User::$email: unknown key "nmae" in config/broken.yaml at $['App\User'].properties.email.nmae
  - App\User::$secret: property is not mapped in config/broken.yaml at $['App\User'].properties.secret
  - App\User: unknown key "colour" in config/broken.yaml at $['App\User'].colour
  - App\User::$id: Invalid template arguments, "," or ">" is expected in config/broken.yaml at $['App\User'].properties.id.type
```

A file that cannot be read or decoded, a document that is not a map, a
top-level key that is not a string, or one class listed twice in a file is a
`ConfigLoadingException`, thrown on the first class the mapper asks about.
Every exception of metadata reading extends
`TypeLang\Mapper\Mapping\Exception\ReaderException`.

## Caching

Reading metadata means reflection, attribute instantiation, docblock and
config parsing. Within one process the result is kept in memory anyway; to
keep it between requests, pass a PSR-6 pool or a PSR-16 cache (install
`psr/cache` or `psr/simple-cache` and an implementation, such as
`symfony/cache`):

```php
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;

final class User
{
    public int $id;

    public string $name;
}

$cache = new Psr16Cache(new FilesystemAdapter('mapper', 0, __DIR__ . '/var/cache'));

$mapper = new Mapper(metadata: MetadataProviderFactory::create(
    cache: $cache,
    prefix: 'app_mapper_',
));

var_dump($mapper->denormalize(['id' => 42, 'name' => 'Jane'], User::class));
```

```
object(User)#905 (2) {
  ["id"]=>
  int(42)
  ["name"]=>
  string(4) "Jane"
}
```

The key of a class is the prefix, the version of the metadata format and a
hash of the class name, for example
`app_mapper_v1_4e488aa01e8d310daa16a2266dc66874`. Only successfully read
metadata is stored, errors are not cached.

There is no automatic invalidation: the cache does not notice that a class,
an attribute or a config file has changed. Clear the cache on deploy, or
change the prefix, which makes every old entry a miss. Two mappers with
different readers or naming strategies sharing one pool need different
prefixes.

<warning>
The cache stores the metadata objects, which the cache implementation
serializes and the mapper restores as they are, without any check. An entry
written by someone else can change how your classes are mapped, for example
make a private property writable. Use a store owned by the application, never
a cache shared with untrusted code.
</warning>

A failure of the cache backend is treated as a miss: the metadata is read
from the sources and the write is skipped. A key the cache rejects, usually
because of a prefix with reserved characters, is an error
(`InvalidCacheKeyException`), here with `prefix: 'app:mapper:'` and
`symfony/cache`:

```
Type "User" cannot be built: Cache rejects the key "app:mapper:v1_4e488aa01e8d310daa16a2266dc66874" of metadata, check the prefix of the keys: Cache key "app:mapper:v1_4e488aa01e8d310daa16a2266dc66874" contains reserved characters "{}()/\@:".
```

## Custom Readers

A reader implements `MetadataReaderInterface`. It receives the class and the
metadata read by the lower readers, and returns that metadata with its own
values. The metadata objects are immutable: `withProperty()`,
`withExternalName()`, `withReadable()` and the other `with*()` methods return
changed copies. A reader overrides properties that are already there and
does not add new ones.

This reader takes a `#[WriteOnly]` attribute of your own and excludes the
property from normalization:

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;
use TypeLang\Mapper\Mapping\Reader\MetadataReaderInterface;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class WriteOnly {}

final class WriteOnlyMetadataReader implements MetadataReaderInterface
{
    public function read(\ReflectionClass $class, ?ClassMetadata $metadata = null): ClassMetadata
    {
        $metadata ??= new ClassMetadata($class->getName());

        foreach ($metadata->properties as $name => $property) {
            $reflection = $class->getProperty($name);

            if ($reflection->getAttributes(WriteOnly::class) !== []) {
                $metadata = $metadata->withProperty($property->withReadable(false));
            }
        }

        return $metadata;
    }
}

final class Account
{
    public string $login;

    #[WriteOnly]
    public string $password;
}

$mapper = new Mapper(metadata: MetadataProviderFactory::create(
    readers: [new WriteOnlyMetadataReader()],
));

$account = $mapper->denormalize(['login' => 'jane', 'password' => 'secret'], Account::class);

var_dump($account->password);
var_dump($mapper->normalize($account));
```

```
string(6) "secret"
array(1) {
  ["login"]=>
  string(4) "jane"
}
```

A reader that finds invalid values should not stop at the first one: the
built-in readers collect them and throw one `InvalidMetadataException` with
the metadata read despite them, so that the readers above still run and all
errors are reported together.

Any reader, the whole chain, or the result of
`MetadataProviderFactory::create()` can also be asked directly, which is
handy to check what the mapper sees:

```php
$metadata = $provider->read(new \ReflectionClass(User::class));
```
