# Introduction

The vast majority of PHP developers encounter the type system used in docblocks 
on a daily basis. For example, you may have seen constructs like these:

```php
/**
 * @param int[] $param Example array parameter
 *
 * @return int An example return type
 *
 * @throws \OutOfBoundsException in case of something went wrong
 */
function example(array $param): int {}
```

There are many possible variations of tags (identifiers starting with the `@` 
symbol) in docblocks (phpdoc), however, they all share one common feature: the 
use of type declaration syntax. For example, in the case of the `@param` tag,
the type is `int[]`, while in the case of `@throws`, it is `\OutOfBoundsException`.

The TypeLang and grammar describes exclusively the syntax of such types, which can 
be used for various tasks; In the vast majority of cases, these will be docblocks,
but other use cases are possible.

On the documentation pages you will find both a description of the syntax of this 
type description language and a comparison table of supported features between 
different tools: TypeLang, PHPStan, Psalm, etc.
