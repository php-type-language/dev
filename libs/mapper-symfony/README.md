<a href="https://github.com/php-type-language" target="_blank">
    <img align="center" src="https://github.com/php-type-language/.github/blob/master/assets/dark.png?raw=true">
</a>

<p align="center">
    <a href="https://packagist.org/packages/type-lang/mapper-symfony"><img src="https://poser.pugx.org/type-lang/mapper-symfony/require/php?style=for-the-badge" alt="PHP 8.1+"></a>
    <a href="https://packagist.org/packages/type-lang/mapper-symfony"><img src="https://poser.pugx.org/type-lang/mapper-symfony/version?style=for-the-badge" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/type-lang/mapper-symfony"><img src="https://poser.pugx.org/type-lang/mapper-symfony/v/unstable?style=for-the-badge" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/php-type-language/mapper-symfony/blob/master/LICENSE"><img src="https://poser.pugx.org/type-lang/mapper-symfony/license?style=for-the-badge" alt="License MIT"></a>
</p>
<p align="center">
    <a href="https://github.com/php-type-language/mapper-symfony/actions/workflows/tests.yml"><img src="https://img.shields.io/github/actions/workflow/status/php-type-language/mapper-symfony/tests.yml?label=Tests&style=flat-square&logo=unpkg"></a>
</p>

---

Symfony integration of the [TypeLang mapper](https://packagist.org/packages/type-lang/mapper):
the bundle registers the mappers as services and configures them from
`config/packages/type_lang_mapper.yaml`.

Full documentation is available at
[typelang.dev](https://typelang.dev/mapper/integrations/symfony/).

## Installation

Install the package via [Composer](https://getcomposer.org):

```sh
composer require type-lang/mapper-symfony
```

With Symfony Flex the bundle is enabled automatically. Otherwise add it to
`config/bundles.php`:

```php
return [
    // ...
    TypeLang\Mapper\Bridge\Symfony\TypeLangMapperBundle::class => [
        'all' => true,
    ],
];
```

**Requirements:**
- PHP 8.1+
- Symfony LTS 6.4+

## Usage

The default mapper is autowired by `MapperInterface`, `DenormalizerInterface`
or `NormalizerInterface`:

```php
use TypeLang\Mapper\DenormalizerInterface;

final class CreateUserController
{
    public function __construct(
        private readonly DenormalizerInterface $mapper,
    ) {}

    public function __invoke(Request $request): Response
    {
        $user = $this->mapper->denormalize(
            value: $request->toArray(),
            type: UserRequestDTO::class,
        );
        // ...
    }
}
```

A named mapper takes the settings of the default one and overrides some of
them:

```yaml
# config/packages/type_lang_mapper.yaml
type_lang_mapper:
  naming: snake_case
  types:
    - App\Mapper\UuidTypeBuilder
  mappers:
    api:
      strict: true
      types:
        - App\Mapper\MoneyTypeBuilder
```

It is passed by its service id, the interfaces are always autowired to the
default mapper:

```php
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class ImportController
{
    public function __construct(
        // type_lang_mapper.mapper: snake_case
        private readonly DenormalizerInterface $mapper,
        // type_lang_mapper.mapper.api: snake_case, strict
        #[Autowire(service: 'type_lang_mapper.mapper.api')]
        private readonly DenormalizerInterface $apiMapper,
    ) {}
}
```

An extension is listed in the config or marked with an attribute:

```php
use TypeLang\Mapper\Bridge\Symfony\Attribute\AsMapperTypeBuilder;

// the default mapper and all named ones
#[AsMapperTypeBuilder]
final class UuidTypeBuilder implements TypeBuilderInterface {}

// the "api" mapper only
#[AsMapperTypeBuilder(mappers: ['api'], priority: 10)]
final class MoneyTypeBuilder implements TypeBuilderInterface {}
```
