# Metadata Providers

Metadata providers are responsible for "freezing" (building) and 
providing "frozen" metadata.

One of the main ones is `MetadataBuilder`,  which contains the rules for 
transforming the mutable read data from  [metadata readers](meta-reader.md) and 
assembling it into the final immutable result.

```php
use TypeLang\Mapper\Mapping\Provider\MetadataBuilder;

$provider = new MetadataBuilder(
    [[[reader: $reader,|meta-reader.md]]]
);
```

During metadata assembly, a `MetadataBuilder` may require a number of
additional optional dependencies. All of these can be passed to the constructor:
- [Expressions Executor](meta-expressions.md) - Executing expressions within metadata
- [PSR-20 Clock](meta-clock.md) - Explicitly specifying the metadata generation time
- [Reference Resolver](meta-references.md) - Type dependencies inference (also called "linking")

Information about other built-in providers is available on the corresponding 
documentation pages:
- [In-Memory Provider](meta-provider-in-memory.md) - Metadata memoization (RAM Cache)
- [PSR-6 Cache Provider](meta-provider-psr6-cache.md) - Metadata caching using [PSR-6 Cache](https://www.php-fig.org/psr/psr-6/) drivers
- [PSR-16 Cache Provider](meta-provider-psr16-cache.md) - Metadata caching using [PSR-16 Cache](https://www.php-fig.org/psr/psr-16/) drivers
- [Null Provider](meta-provider-null.md) - Returns empty metadata objects

You can pass the metadata provider to [a platform](standard-platform.md) that 
uses types that require metadata. So the complete example of using providers 
would look like this:

<include from="type-metadata.md" 
    element-id="composition-example" />