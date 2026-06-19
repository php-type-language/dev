# References Resolving

After reading the metadata, you need to "link" the types. For example, 
if you use [the phpdoc reader](meta-reader-phpdoc.md), you need to 
understand that the specified type of the relation is 
`\App\Example\MessageResult` itself, not `\MessageResult` 
from the global namespace.

```php
namespace App\Example;

final readonly class MessagesListResult
{
    public function __construct(
        /**
         * @var list<MessageResult>
         */
        public array $messages,
        public int $count = 0,
    ) {}
}
```

The same behavior is required, for example, when using
[attributes reader](meta-reader-attribute.md):

```php
#[MapType('list<MessageResult>')]
public array $messages,
// ...
```

For linking (resolving) types, a separate subsystem is used that implements
the `TypeLang\Mapper\Mapping\Reference\Reader\ReferencesReaderInterface` interface.

By default, two implementations are available: 
- `NullReferencesReader` - Does nothing (Returns nothing) 
- `NativeReferencesReader` Infers types from the namespace of the class and the 
  specified `use` statements.

You can replace the implementation with your own if needed:

```php
use TypeLang\Mapper\Mapping\Reference\Reader\ReferencesReaderInterface;
use TypeLang\Mapper\Mapping\Provider\MetadataBuilder;

$reader = new class implements ReferencesReaderInterface {
    public function getUseStatements(\ReflectionClass $class): array
    {
        return [
            // If a class references to "Alias", then
            // the "Path\To\RealClassName" will be resolved
            'Alias' => Path\To\RealClassName::class,

            // If a class references to "Name", then
            // the "Another\Class\Name" will be resolved
            Another\Class\Name::class,
        ];
    }
};

$provider = [[[new MetadataBuilder(|meta-provider.md]]]
    references: $reader,
);
```