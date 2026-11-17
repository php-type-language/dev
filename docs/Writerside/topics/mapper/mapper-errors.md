# Errors

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

| Exception                 | When                                                 |
|---------------------------|------------------------------------------------------|
| `InvalidValueException`   | the data does not match the type                     |
| `TypeDefinitionException` | the type is unknown, written or declared wrong       |

Both are in the `TypeLang\Mapper\Exception` namespace and implement
`MapperExceptionInterface`.

```php
$mapper->denormalize(['id' => 'abc'], User::class);
// throws InvalidValueException: $.id: Value must be of type "int", but "abc"
// given

$mapper->denormalize([], 'list<int');
// throws TypeDefinitionException: Type "list<int" is written wrong: Invalid
// template arguments, "," or ">" is expected

$mapper->denormalize([], 'unknown');
// throws TypeDefinitionException: Type "unknown" is not supported
```

## Violations

An `InvalidValueException` contains a list of violations. Each one has a
code, a path, the rejected value and the parameters of its message.

```php
use TypeLang\Mapper\Exception\InvalidValueException;

try {
    $mapper->denormalize(['id' => 'abc'], User::class);
} catch (InvalidValueException $e) {
    $violation = $e->violations[0];

    $violation->code;       // ViolationCode::InvalidType
    $violation->value;      // 'abc'
    $violation->parameters; // ['expected' => 'int']
}
```

| Code             | Message                                                 |
|------------------|---------------------------------------------------------|
| `invalid_type`   | `Value must be of type "{{expected}}", but {{value}} given` |
| `invalid_key`    | `Key must be of type "{{expected}}", but {{value}} given`   |
| `missing_field`  | `Required field "{{field}}" is missing`                 |
| `unexpected_field` | `Unexpected field "{{field}}"`                        |
| `failed_check`   | `Value must be of type "{{expected}}" ({{check}})`      |
| `invalid_value`  | `{{message}}`, the message of an exception of a hook or a custom type |

## Paths

The path of a violation is printed as JSONPath. Properties renamed by
metadata have the names of the data.

```php
$mapper->denormalize(['items' => [['id' => 1], ['id' => 'x']]], Order::class);
// throws InvalidValueException: $.items[1].id: Value must be of type "int", but
// "x" given
```

## Collecting Violations

By default the first violation fails. To collect several of them, use the
[max violations](mapper-configuration.md#max-violations) option.

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

$mapper = new Mapper(new Configuration(maxViolations: 100));

try {
    $mapper->denormalize(['id' => 'abc'], User::class);
} catch (InvalidValueException $e) {
    $e->getMessage();
    // '$.id: Value must be of type "int", but "abc" given (and 1 more)'

    \count($e->violations); // 2
}
```

## Messages

Every message is formatted by the template of the code. Custom templates are
set by [metadata](mapper-metadata-errors.md).

To return errors from an API, format the violations yourself:

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Diagnostics\Printer\Path\JsonPathPrinter;
use TypeLang\Mapper\Diagnostics\ViolationFormatter;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

$mapper = new Mapper(new Configuration(maxViolations: 100));
$paths = new JsonPathPrinter();
$messages = new ViolationFormatter();

try {
    $mapper->denormalize(['id' => 'abc'], User::class);
} catch (InvalidValueException $e) {
    $errors = [];

    foreach ($e->violations as $violation) {
        $path = $paths->print($violation->path);
        $errors[$path] = $messages->format($violation);
    }

    $errors;
    // ['$.id' => 'Value must be of type "int", but "abc" given', '$.name' =>
    // 'Required field "name" is missing']
}
```

<warning>
<code>Violation::$value</code> is the raw rejected value: do not log it as is
when the data may contain secrets. The messages of
<code>TypeDefinitionException</code> may contain paths of your files: do not
return them to clients.
</warning>
