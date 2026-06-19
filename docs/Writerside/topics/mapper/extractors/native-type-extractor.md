# Native Type Extractor

<show-structure for="chapter" depth="2"/>

<link-summary>
The native (default) type extractor provides general rules for inferring 
types from values.
</link-summary>

The native (default) type extractor provides general rules for inferring 
types from values.

This extractor is available as `TypeLang\Mapper\Type\Extractor\NativeTypeExtractor` 
class, which you can make by hand if you wish.

```php
use TypeLang\Mapper\Type\Extractor\NativeTypeExtractor;

$extractor = new NativeTypeExtractor();
```

To create a mapper inside it, the associated factory 
`TypeLang\Mapper\Type\Extractor\Factory\DefaultTypeExtractorFactory` is used.

```php
use TypeLang\Mapper\Type\Extractor\Factory\DefaultTypeExtractorFactory;

$mapper = new Mapper(
    // ...
    typeExtractorFactory: new DefaultTypeExtractorFactory(),
);
```

## Inference Rules

Below is a list of supported types. An expression similar to one below will be 
used as an example.

<code-block lang="php">
use TypeLang\Mapper\Type\Extractor\NativeTypeExtractor;

$extractor = new NativeTypeExtractor();

echo $extractor->getDefinitionByValue(&lt;expr&gt;);
</code-block>


### String Value

When passing a PHP `string` value, the `"string"` will be returned.

<code-block lang="php">
echo $extractor->getDefinitionByValue("string");
// string
</code-block>


### Boolean Value

When passing a PHP `bool` value, the `"bool"` will be returned.

<code-block lang="php">
echo $extractor->getDefinitionByValue(true);
// bool
</code-block>


### Integer Value

When passing a PHP `int` value, the `"int"` will be returned.

<code-block lang="php">
echo $extractor->getDefinitionByValue(42);
// int
</code-block>


### Float Value

When passing a PHP `float` value, the `"float"` will be returned.

<code-block lang="php">
echo $extractor->getDefinitionByValue(0.42);
// float
</code-block>


### Null Value

When passing a PHP `null` value, the `"null"` will be returned.

<code-block lang="php">
echo $extractor->getDefinitionByValue(null);
// null
</code-block>


### Resource Value

When passing a PHP `resource` value, the `"resource"` will be returned.

<code-block lang="php">
$resource = fopen(__FILE__, 'rb');

echo $extractor->getDefinitionByValue($resource);
// resource

fclose($resource);

echo $extractor->getDefinitionByValue($resource);
// resource
</code-block>


### Array Value

When passing a PHP `array` value, the `"array"` will be returned.

<code-block lang="php">
echo $extractor->getDefinitionByValue([1, 2, 3]);
// array
</code-block>


### Object Value

When passing a PHP `object` value, the `"object"` or **specific class name** 
will be returned.

<code-block lang="php">
// Instance of a specific class "Example\User" 
// should return "Example\User" type
echo $extractor->getDefinitionByValue(new Example\User());


// Instance of an anonymous object
// should return "stdClass" type
echo $extractor->getDefinitionByValue((object) ['some' => 'any']);


// Instance of an anonymous class
// should return "object" type
echo $extractor->getDefinitionByValue(new class {});
</code-block>