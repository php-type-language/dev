# Attribute Reader

<link-summary>
Reads metadata using PHP attributes
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Mapping\Reader\AttributeReader</code>
    </p>
</tldr>

This reader is used to read PHP attributes to construct metadata.

To create it, it is enough to instantiate `AttributeReader` class:

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Platform\StandardPlatform;
use TypeLang\Mapper\Mapping\Reader\AttributeReader;

$reader = new AttributeReader();

$mapper = new Mapper(
    [[[platform: new StandardPlatform(|standard-platform.md]]]
        meta: $reader,
        // ...
    ),
);
```

After this, you will have access to the description of the types
in the PHP Attributes:

```php
use TypeLang\Mapper\Mapping\MapType;

final class UserInfo
{
    public function __construct(
        #[MapType('non-empty-string')]
        public mixed $name,
    ) {}
}
```

By default, it supports the following attributes:
- `#[DiscriminatorMap]` - Allows to specify [a discriminator map](meta-configuration.md#discriminator-map) 
  for a class or interface
- `#[MapName]` - Allows to specify [public aliases](meta-configuration.md#rename-alias)
  for properties
- `#[MapType]` - Allows to specify [property type](meta-configuration.md#properties)
  and [type strictness](meta-configuration.md#strict-types)
- `#[NormalizeAsArray]` - Allows to specify [class normalization](meta-configuration.md#normalize-as-array) 
  rules (into an associative array or object)
- `#[OnTypeError]` - Allows to customize [the type error](meta-configuration.md#custom-type-error-message)
  in a property
- `#[OnUndefinedError]` - Allows to customize [the "undefined property" error](meta-configuration.md#custom-undefined-error-message)
  in a property
- `#[SkipWhen]` - Allows to specify an [expression](meta-configuration.md#expression) that will exclude 
  a property during normalization
- `#[SkipWhenEmpty]` - Allows to specify a ["when empty" rule](meta-configuration.md#when-empty) 
  that will exclude a property during normalization
- `#[SkipWhenNull]` - Allows to specify a ["when null" rule](meta-configuration.md#when-null)
  that will exclude a property during normalization

<tip>
You can find more information about <b>metadata configuration</b> rules
in the <a href="meta-configuration.md">"metadata configuration" section</a>.
</tip>

If you need to supplement metadata from another reader, you should
specify this explicitly using `$delegate` argument:

```php
use TypeLang\Mapper\Mapping\Reader\AttributeReader;

$reader = new AttributeReader(
    delegate: new AnotherExampleReader(),
);
```