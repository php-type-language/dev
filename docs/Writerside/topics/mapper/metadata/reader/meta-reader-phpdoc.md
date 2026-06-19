# PHPDoc Reader

<link-summary>
Reads metadata using PHPDoc annotations
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Mapping\Reader\PhpDocReader</code>
    </p>
</tldr>

This reader is used to read PHPDoc annotations to construct metadata.

To use it, you need to install an additional package, which provides the 
ability to read docblocks:

```shell
composer require type-lang/phpdoc type-lang/phpdoc-standard-tags
```

To create this reader, it is enough to instantiate `PhpDocReader` class:

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Platform\StandardPlatform;
use TypeLang\Mapper\Mapping\Reader\PhpDocReader;

$reader = new PhpDocReader();

$mapper = new Mapper(
    [[[platform: new StandardPlatform(|standard-platform.md]]]
        meta: $reader,
        // ...
    ),
);
```

After this, you will have access to the description of the types 
in the PHPDoc tags:

```php
final class UserInfo
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public mixed $name,
    ) {}
}
```

By default, this reader supports the following annotations, which directly 
or indirectly describe types for properties:

- `@param` - The annotation name can be overridden by specifying the
  `$paramTagName` constructor argument explicitly
- `@var` - The annotation name can be overridden by specifying the
  `$varTagName` constructor argument explicitly
- `@return` - The annotation name can be overridden by specifying the
  `$returnTagName` constructor argument explicitly

<tip>
You can find more information about <b>metadata configuration</b> rules
in the <a href="meta-configuration.md">"metadata configuration" section</a>.
</tip>

If you need to supplement metadata from another reader, you should
specify this explicitly using `$delegate` argument:

```php
use TypeLang\Mapper\Mapping\Reader\PhpDocReader;

$reader = new PhpDocReader(
    delegate: new AnotherExampleReader(),
);
```

In addition, you can create fallback rules for annotations in the same way: 
"first with a prefix, then the regular name"

To create this reader, it is enough to instantiate `PhpDocReader` class:

```php
use TypeLang\Mapper\Mapping\Reader\PhpDocReader;

// First, we read all annotations 
// with the "map-" prefix.
$reader = new PhpDocReader(
    paramTagName: 'map-param',
    varTagName: 'map-var',
    returnTagName: 'map-return',
    // Then everyone else
    delegate: new PhpDocReader(
        // Then we can turn to another reader,
        // for example, reflection
        delegate: ...
    ),
);
```

In this case you can use override for types, for example:

```php
final class UserInfo
{
    public function __construct(
        /**
         * Psalm, PHPStan, PHPStorm and others will read the type
         * defined in "var" annotation. However, for the mapper
         * we override it to another using "map-var" tag
         *
         * @var non-empty-string
         * @map-var string 
         */
        public string $name,
    ) {}
}
```