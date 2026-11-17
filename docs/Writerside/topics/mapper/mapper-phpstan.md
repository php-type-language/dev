# PHPStan

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

The PHPStan extension reports type strings written wrong in calls of
`denormalize()`, `isDenormalizable()`, `normalize()`, `isNormalizable()` and in
`#[MapType]`.

```php
$mapper->denormalize($data, 'list<Item'); // reported

#[MapType('array{id: int')]                // reported
public array $data;
```

```
 ------ ---------------------------------------------------------------
  Line   example.php
 ------ ---------------------------------------------------------------
  12     Type "list<Item" is written wrong: Invalid template
         arguments, "," or ">" is expected
         🪪  typeLangMapper.typeSyntax
 ------ ---------------------------------------------------------------
```

## Installation

With [phpstan/extension-installer](https://github.com/phpstan/extension-installer)
the extension is enabled automatically. Otherwise include it in
`phpstan.neon`:

```neon
includes:
    - vendor/type-lang/mapper/libs/bridge/phpstan/extension.neon
```

## Limitations

- Only constant strings are checked, a type in a variable is not.
- Only the syntax is checked: an unknown type such as `list<Itme>` is not
  reported.
- Types in PHPDoc and config files are not checked.
