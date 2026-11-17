# Metadata

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

Metadata describes how a class is mapped: which properties, of which types,
under which names. Without any metadata the mapper uses the class as it is:

- only **public non-static** properties are mapped;
- the type of a property is its native type;
- an object is created **without calling the constructor**, default values
  of properties are kept.

```php
$mapper->denormalize(['id' => 1, 'name' => 'Alice'], User::class); // User { id: 1, name: 'Alice', role: 'user', password: '' }
```

Everything else is described with metadata: see
[writing metadata](mapper-metadata-options.md).

## Sources

| Source         | Example                                  | Requires                    |
|----------------|------------------------------------------|-----------------------------|
| Attributes     | `#[MapType('list<string>')]`             |                             |
| PHPDoc         | `@var list<string>`                      | `type-lang/phpdoc`          |
| Reflection     | `public array $tags`                     |                             |
| PHP Config     | `return ['App\User' => [...]];`          |                             |
| JSON Config    | `{"App\\User": {...}}`                   |                             |
| YAML Config    | `App\User: ...`                          | `symfony/yaml`              |
| NEON Config    | `App\User: ...`                          | `nette/neon`                |

Attributes, PHPDoc and reflection are always enabled, config files are
[enabled explicitly](#config-files). Priority, from the highest: config
files, attributes, PHPDoc, reflection.

## Config Files

A config file is a map of classes to their metadata. Pass the readers to the
metadata provider:

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;
use TypeLang\Mapper\Mapping\Reader\YamlFileMetadataReader;

$mapper = new Mapper(
    metadata: MetadataProviderFactory::create(
        readers: [
            new YamlFileMetadataReader(__DIR__ . '/mapping.yaml'),
        ],
    ),
);
```

| Format | Reader                   |
|--------|--------------------------|
| PHP    | `PhpFileMetadataReader`  |
| JSON   | `JsonFileMetadataReader` |
| YAML   | `YamlFileMetadataReader` |
| NEON   | `NeonFileMetadataReader` |
| array  | `ArrayMetadataReader`    |

```yaml
App\User:
    normalize_as_array: true
    properties:
        id: positive-int
        email:
            name: e_mail
            type: non-empty-string
```

<note>
A wrong key or value of a config is an error with the file and the path:
<code>App\User::$email: unknown key "nmae" in mapping.yaml at
$['App\User'].properties.email.nmae</code>
</note>

## Caching

Reading metadata (especially PHPDoc) is slow. Pass a PSR-6 or PSR-16 cache:

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MetadataProviderFactory;

$mapper = new Mapper(
    metadata: MetadataProviderFactory::create(
        cache: $psr16cache,
    ),
);
```

<warning>
The cache is not invalidated when a class changes: clear it on deploy.
</warning>

## Naming Strategy

To rename the fields of all classes at once, see
[naming strategies](mapper-naming.md).
