# Diagnostics

Describes data errors of mapping: where in the input and the type, what code, what text.

```php
use TypeLang\Mapper\Diagnostics\Path\PathStack;
use TypeLang\Mapper\Diagnostics\Path\Segment\PropertySegment;
use TypeLang\Mapper\Diagnostics\Path\Segment\UnionLeafSegment;
use TypeLang\Mapper\Diagnostics\Printer\Path\JsonPathPrinter;

$path = new PathStack();
$path->enter(new PropertySegment(Order::class, 'items'));
$path->enter(3);
$path->enter(new UnionLeafSegment(1, $itemType));

echo (new JsonPathPrinter())->print($path->capture());
// $.items[3]
```
