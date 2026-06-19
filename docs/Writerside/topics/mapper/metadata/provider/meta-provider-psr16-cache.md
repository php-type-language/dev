# PSR-16 Cache Metadata Provider


<link-summary>
Returns metadata using intermediate caching in the PSR-16 cache
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Mapping\Provider\Psr16CacheProvider</code>
    </p>
</tldr>

Returns metadata using intermediate caching in the PSR-16 cache

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Platform\StandardPlatform;
use TypeLang\Mapper\Mapping\Provider\Psr16CacheProvider;

$provider = new Psr6CacheProvider(
    // required PSR-16 cache pool
    [[[psr16: $psr6cache|https://www.php-fig.org/psr/psr-16/]]],
    // optional prefix
    prefix: 'mapper_',
    // optional cache TTL
    ttl: -1,
    // Reference to parent reader or provider
    delegate: new AnotherMetadataProvider(),
);

$mapper = new Mapper(
    [[[platform: new StandardPlatform(|standard-platform.md]]]
        meta: $provider,
    ),
);
```