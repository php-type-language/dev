# Type Extractor

A type extractor is a class responsible for inferring a type from a value.

An extractor can be used both when [normalizing](data-mapper.md#usage) an 
arbitrary value with a mapper and when using the built-in 
[mixed type](mixed-type.md).

The mapper supports a built-in [native (default) type extractor](native-type-extractor.md),
but you can specify [your own if necessary](custom-type-extractor.md).

To specify an extractor, you must pass the desired factory to the `Mapper` 
constructor, which will create an extractor object.

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Type\Extractor\Factory\DefaultTypeExtractorFactory;

$mapper = new Mapper(
    // ...
    typeExtractorFactory: new DefaultTypeExtractorFactory(),
);
```