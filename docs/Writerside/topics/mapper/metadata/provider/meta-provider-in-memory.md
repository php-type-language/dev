# InMemory Metadata Provider

<link-summary>
Caches metadata in RAM
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Mapping\Provider\InMemoryProvider</code>
    </p>
</tldr>

The in-memory provider returns metadata objects with intermediate 
caching of types in RAM.

To create an in-memory provider you should create a new
`InMemoryProvider` instance, parent another provider
(or [metadata reader](meta-reader.md)) in the constructor.

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Platform\StandardPlatform;
use TypeLang\Mapper\Mapping\Provider\InMemoryProvider;

$provider = new InMemoryProvider(
    delegate: new AnotherMetadataProvider(),
);

$mapper = new Mapper(
    [[[platform: new StandardPlatform(|standard-platform.md]]]
        meta: $provider,
        // ...
    ),
);
```