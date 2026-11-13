# Diagnostics

Describes data errors of mapping: where in the input and the type, what code, what text.

```php
use TypeLang\Mapper\Diagnostics\Path\PathStack;
use TypeLang\Mapper\Diagnostics\Printer\Path\JsonPathPrinter;

$path = new PathStack();
$path->enterIntoObject(Order::class);
$path->enterIntoObjectProperty('items');
$path->enterIntoArrayIndex(3);
$path->enterIntoUnionLeaf(1);

echo (new JsonPathPrinter())->print($path->capture());
// $.items[3]
```
