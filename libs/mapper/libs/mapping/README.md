# Mapping

Mapping of classes: attributes, metadata model and its reading from configs, attributes, phpdoc and reflection.

```php
use TypeLang\Mapper\Mapping\MetadataProviderFactory;
use TypeLang\Mapper\Mapping\Reader\YamlFileMetadataReader;
use TypeLang\Mapper\Naming\NamingStrategy;

$provider = MetadataProviderFactory::create(
    readers: [new YamlFileMetadataReader(__DIR__ . '/mapping.yaml')],
    strategy: NamingStrategy::SnakeCase,
);

$metadata = $provider->read(new \ReflectionClass(App\Model\User::class));
```
