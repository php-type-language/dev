# Symfony

<primary-label ref="mapper-symfony"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

The bundle registers mappers as services of a Symfony 6.4+ application.

```shell
composer require type-lang/mapper-symfony
```

With Symfony Flex the bundle is enabled automatically. Otherwise add it to
`config/bundles.php`:

```php
return [
    // ...
    TypeLang\Mapper\Bridge\Symfony\TypeLangMapperBundle::class => ['all' => true],
];
```

## Mappers

The default mapper is the `type_lang_mapper.mapper` service, autowired by
`MapperInterface`, `DenormalizerInterface` and `NormalizerInterface`.

```php
use TypeLang\Mapper\DenormalizerInterface;

final class CreateUserController
{
    public function __construct(
        private readonly DenormalizerInterface $mapper,
    ) {}

    public function __invoke(Request $request): Response
    {
        $user = $this->mapper->denormalize($request->toArray(), CreateUser::class);
        // ...
    }
}
```

A named mapper takes the settings of the default one and overrides some of
them. It is the `type_lang_mapper.mapper.<name>` service, autowired by the
same interfaces with the `$<name>Mapper` argument name.

```yaml
# config/packages/type_lang_mapper.yaml
type_lang_mapper:
  naming: snake_case
  mappers:
    api:
      strict: true
```

```php
public function __construct(
    private readonly DenormalizerInterface $mapper,    // type_lang_mapper.mapper
    private readonly DenormalizerInterface $apiMapper, // type_lang_mapper.mapper.api
) {}
```

<note>
A setting a named mapper leaves unset is the one of the default mapper. The
lists (types, hooks, readers, ...) of a named mapper go before the ones of
the default mapper.
</note>

## Configuration

```yaml
# config/packages/type_lang_mapper.yaml
type_lang_mapper:
  # see "Configuration" for the meaning of the options
  strict: false
  normalize_as_array: true
  max_violations: 1
  reject_extra_fields: false
  expected_type_depth: 1
  expected_type_max_fields: ~

  # snake_case, camelCase, PascalCase, SCREAMING_SNAKE_CASE, kebab-case
  # or a NamingStrategyInterface service
  naming: ~

  # services, see "Extensions"
  types: []
  decorators: []
  expanders: []
  hooks: []
  instantiators: []
  accessors: []

  # CoercerInterface service by the type
  coercers:
    int: ~
    float: ~
    string: ~
    bool: ~
    array: ~

  metadata:
    # MetadataReaderInterface services over the default readers
    readers: []

    # config files or directories of them (yaml, yml, neon, json, php)
    paths:
      - '%kernel.project_dir%/config/mapping'

    # PropertySelectorInterface service
    selector: ~

    cache:
      # default = false in debug mode, true otherwise
      enabled: ~
      # from the first one checked to the last one,
      # default = the "cache.type_lang_mapper" pool
      pools:
        - { psr16: app.redis, prefix: hot_ }
        - { psr6: cache.app }

  # mappers extending the default one, by the name
  mappers:
    api:
      strict: true

  # default = enabled in debug mode
  profiler: ~
```

The options are described in [Configuration](mapper-configuration.md),
[Naming Strategy](mapper-metadata-naming.md#naming-strategy),
[Config Files](mapper-metadata.md#config-files) and
[Caching](mapper-metadata.md#caching).

## Extensions

An extension is added to the mappers by an attribute of an autoconfigured
service.

```php
use TypeLang\Mapper\Bridge\Symfony\Attribute\AsMapperTypeBuilder;

#[AsMapperTypeBuilder]                     // the default mapper and named ones
final class UuidTypeBuilder implements TypeBuilderInterface {}

#[AsMapperTypeBuilder(mappers: ['api'])]   // the "api" mapper only
final class MoneyTypeBuilder implements TypeBuilderInterface {}

#[AsMapperTypeBuilder(priority: 10)]       // before the other ones
final class DecimalTypeBuilder implements TypeBuilderInterface {}
```

| Attribute                | Interface                        | Config key         |
|--------------------------|----------------------------------|--------------------|
| `AsMapperTypeBuilder`    | `TypeBuilderInterface`           | `types`            |
| `AsMapperDecorator`      | `TypeDecoratorInterface`         | `decorators`       |
| `AsMapperTypeExpander`   | `TypeExpanderInterface`          | `expanders`        |
| `AsMapperHook`           | `ClassHookInterface`             | `hooks`            |
| `AsMapperInstantiator`   | `InstantiationStrategyInterface` | `instantiators`    |
| `AsMapperAccessor`       | `AccessStrategyInterface`        | `accessors`        |
| `AsMapperMetadataReader` | `MetadataReaderInterface`        | `metadata.readers` |

The same services are listed in the config by their ids. The services of
the config go first, in the order of the config, then the ones of the
attributes.

```yaml
type_lang_mapper:
  types:
    - App\Mapper\UuidTypeBuilder
```

A coercer replaces the one of the type, see
[Custom Coercers](mapper-coercion.md#custom-coercers):

```php
use TypeLang\Mapper\Bridge\Symfony\Attribute\AsMapperCoercer;

#[AsMapperCoercer('float', mappers: ['api'])]
final class DecimalCommaCoercer implements CoercerInterface {}
```

<warning>
A mapper takes one coercer per type: two coercers of the same type for the
same mapper are an error.
</warning>

## Profiler

In debug mode the Symfony profiler gets a "Mapper" panel: the calls of each
mapper with their input, output and violations, the place of the call, the
built types and the metadata reads. With `symfony/stopwatch` the calls are
also shown on the performance timeline.

```yaml
type_lang_mapper:
  profiler: false
```
