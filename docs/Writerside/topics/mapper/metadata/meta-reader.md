# Metadata Readers

Readers are responsible for reading and appending metadata from various sources.
As is clear from the logic, all data passing through the reader chain is 
**mutable**.

In most cases, readers are implemented through delegation, so you can configure 
priorities for which data source is most important.

```php
// Top priority
$reader = new ImportantMetadataReader(
    // Uses a delegate to supplement meta
    delegate: new SecondaryMetadataReader(
        // Uses a delegate to supplement meta
        delegate: new OptionalMetadataReader(),
    ),
);
```

This package already provides a built-in set of readers that you can check out:

- [Reflection Reader](meta-reader-reflection.md) - Reads metadata from a native type declarations
- [Attributes Reader](meta-reader-attribute.md) - Reads metadata from [PHP Attributes](https://www.php.net/manual/en/language.attributes.overview.php)
- [PHPDoc Reader](meta-reader-phpdoc.md) - Reads metadata from [PHP DocBlock](https://docs.phpdoc.org/guide/getting-started/what-is-a-docblock.html)
- [Array Reader](meta-reader-array.md) - Reads metadata from [PHP Array](https://www.php.net/manual/en/language.types.array.php)
- [PHP Config Reader](meta-reader-php-config.md) - Reads metadata from PHP config files
- [JSON Config Reader](meta-reader-json-config.md) - Reads metadata from JSON config files
- [NEON Config Reader](meta-reader-neon-config.md) - Reads metadata from [NEON config files](https://doc.nette.org/en/neon/format)
- [YAML Config Reader](meta-reader-yaml-config.md) - Reads metadata from [YAML config files](https://symfony.com/doc/current/reference/formats/yaml.html)
- [Null Reader](meta-reader-null.md) - Returns empty metadata

You can pass the metadata reader to [a platform](standard-platform.md) that
uses types that require metadata. So the complete example of using readers
would look like this:

<include from="type-metadata.md" 
    element-id="composition-example" />