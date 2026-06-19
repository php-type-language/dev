# Null Reader

<link-summary>
Reads only the minimum required data (class name) from the class definition.
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Mapping\Reader\NullReader</code>
    </p>
</tldr>

This reader returns an empty metadata object containing only 
the required class name.

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Platform\StandardPlatform;
use TypeLang\Mapper\Mapping\Reader\NullReader;

$mapper = new Mapper(
    [[[platform: new StandardPlatform(|standard-platform.md]]]
        meta: new NullReader(),
    ),
);
```

The "class name" will be equivalent to the one specified:

```php
namespace App\Example;

class ExampleClassName
{
    public string $example;
}
```

When using this reader, the class name will be read as:

```php
object(TypeLang\Mapper\Mapping\Metadata\ClassInfo) {
  +name: "App\\Example\\ExampleClassName"
  +properties: []
  // ...etc
}
```

No further information about the class will be specified (i.e. there will be no 
properties or other information in the metadata.

<note>
This reader does not accept any additional arguments in the constructor and 
does not augment (delegate) metadata.
</note>