# Custom Type Extractor

To implement a custom type extractor, you should implement your own class that 
uses the `TypeLang\Mapper\Type\Extractor\Factory\TypeExtractorFactoryInterface` 
interface that provides `getDefinitionByValue()` method.

```php
use TypeLang\Mapper\Type\Extractor\Factory\TypeExtractorFactoryInterface;

class MyTypeExtractor implements TypeExtractorFactoryInterface
{
    public function getDefinitionByValue(mixed $value): string
    {
        // An example extractor that outputs 
        // literal types for PHP scalars
        if (\is_scalar($value)) {
            return \var_export($value, true);
        }

        return \get_debug_type($value);
    }
}
```

<note>
The above is an example of an extractor that will return 
<code>42</code> for <code>42</code> value, <code>"test"</code> for 
<code>"test"</code>, and so on.
</note>


## Extractor Factory

Next, you need to create a factory that will return the specified extractor 
based on the mapper configuration settings.

This factory is responsible for creating an extractor instance depending on 
the mapper settings, which will be placed into the mapper itself.

```php
use TypeLang\Mapper\Configuration;
use TypeLang\Mapper\Type\Extractor\TypeExtractorInterface;
use TypeLang\Mapper\Type\Extractor\Factory\TypeExtractorFactoryInterface;

class MyTypeExtractorFactory implements TypeExtractorFactoryInterface
{
    public function createTypeExtractor(
        [[[Configuration $config,|mapper-configuration.md]]]
    ): TypeExtractorInterface {
        // We can read the settings from "$config" 
        // and return the desired type extractor
        return new MyTypeExtractor();
    }
}
```


## Registration

To register a mapper, you should pass your custom factory inside the 
mapper constructor.

```php
use TypeLang\Mapper\Mapper;

$mapper = new Mapper(
    // ...
    typeExtractorFactory: new MyTypeExtractorFactory(),
);
```

## Usage

After registration, you can test the extractor's functionality. For example, 
you can use the `normalize` mapper's method for this.

```php
use TypeLang\Mapper\Mapper;

$mapper = new Mapper(typeExtractorFactory: ...);

// For the passed value "42", the type "42" will be inferred 
// instead of "int" (the default) and the conversion rules 
// specific to the type "42" will be applied.
$result = $mapper->normalize(42);
```

Similar behavior can occur, for example, when using the `mixed` type:

```php
// The built-in "mixed" type infers the 
// type depending on the value
$result = $mapper->denormalize(42, 'mixed');
```