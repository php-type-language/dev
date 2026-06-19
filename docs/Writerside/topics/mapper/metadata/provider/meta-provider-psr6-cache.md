# PSR-6 Cache Metadata Provider

<link-summary>
Returns metadata using intermediate caching in the PSR-6 cache
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Mapping\Provider\Psr6CacheProvider</code>
    </p>
</tldr>

Returns metadata using intermediate caching in the PSR-6 cache

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Platform\StandardPlatform;
use TypeLang\Mapper\Mapping\Provider\Psr6CacheProvider;

$provider = new Psr6CacheProvider(
    // required PSR-6 cache pool
    [[[psr6: $psr6CachePool|https://www.php-fig.org/psr/psr-6/]]],
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