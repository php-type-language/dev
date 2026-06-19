# Null Metadata Provider

<link-summary>
Returns empty metadata objects
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Mapping\Provider\NullProvider</code>
    </p>
</tldr>

This provider returns an empty metadata object containing only
the required class name.

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Platform\StandardPlatform;
use TypeLang\Mapper\Mapping\Provider\NullProvider;

$mapper = new Mapper(
    [[[platform: new StandardPlatform(|standard-platform.md]]]
        meta: new NullProvider(),
    ),
);
```