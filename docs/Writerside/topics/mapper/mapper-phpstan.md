# PHPStan

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

A type given to the mapper is a string, so a typo in it, such as a missing
`>`, is found only when the code runs and fails with a
[`TypeDefinitionException`](mapper-errors.md#invalid-types). The mapper
ships a [PHPStan](https://phpstan.org) extension that parses these strings
during static analysis and reports the ones written wrong.

## What It Checks

The extension reports a type string with a syntax error in:

- the type argument of `denormalize()`, `isDenormalizable()`,
  `normalize()` and `isNormalizable()`, called on an object PHPStan knows
  to be a `DenormalizerInterface` or a `NormalizerInterface` (the `Mapper`
  is both), passed by position or as the named argument `type`;
- the `type` argument of the `#[MapType]` attribute.

Each error has the identifier `typeLangMapper.typeSyntax`, which can be
used to [ignore](https://phpstan.org/user-guide/ignoring-errors) it like
any other PHPStan error.

## Installation

The extension is part of the `type-lang/mapper` package, only PHPStan
itself is needed:

```bash
composer require --dev phpstan/phpstan
```

With [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer)
the extension is included automatically: the package declares it in
`extra.phpstan.includes` of its `composer.json`.

Without the installer, include it in your `phpstan.neon`:

```yaml
includes:
  - vendor/type-lang/mapper/libs/bridge/phpstan/extension.neon
```

## Example

```php
<?php

declare(strict_types=1);

namespace App;

use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MapType;

final class Order
{
    #[MapType('list<Item')]
    public array $items = [];
}

function load(Mapper $mapper, array $data): void
{
    $mapper->denormalize($data, 'array{id: int, name: string');
    $mapper->normalize($data, 'list<int>');
    $mapper->isDenormalizable($data, 'App\Unknown');
}
```

```
 ------ ----------------------------------------------------------------------------------------------------- 
  Line   example.php                                                                                          
 ------ ----------------------------------------------------------------------------------------------------- 
  12     Type "list<Item" is written wrong: Invalid template arguments, "," or ">" is expected                
         🪪  typeLangMapper.typeSyntax                                                                        
         at example.php:12                                                                                    
  18     Type "array{id: int, name: string" is written wrong: Invalid shape, a field, "," or "}" is expected  
         🪪  typeLangMapper.typeSyntax                                                                        
         at example.php:18                                                                                    
 ------ ----------------------------------------------------------------------------------------------------- 

 [ERROR] Found 2 errors
```

The message is the same as the one of the `TypeDefinitionException` the
mapper would throw at runtime. `list<int>` is valid and is not reported,
neither is `App\Unknown`, see below.

## Limitations

- **Only constant strings are checked.** A literal, a class constant or a
  concatenation of constants is checked. When PHPStan knows a value is one
  of several constant strings, such as from a ternary, each of them is
  checked. A string PHPStan cannot narrow to constants, such as a
  parameter typed `string`, is skipped.
- **Only the syntax is checked.** The extension does not know whether a
  class exists or whether the mapper has a type of the given name: such
  errors are found at runtime only. Types written in PHPDoc and in mapping
  configuration files are not checked either.
- **Only calls on a known mapper are checked.** A `denormalize()` method of
  an object that implements neither interface of the mapper is ignored.
