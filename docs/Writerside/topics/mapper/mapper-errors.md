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
final class User
{
    public int $id;
}

$mapper->denormalize(['id' => 'abc'], User::class);
// throws InvalidValueException: $.id: Value must be of type int, but "abc"
// given

$mapper->denormalize([], 'list<int');
// throws TypeDefinitionException: Type "list<int" is written wrong: Invalid
// template arguments, "," or ">" is expected

$mapper->denormalize([], 'unknown');
// throws TypeDefinitionException: Type "unknown" is not supported
```

## Violations

An `InvalidValueException` contains a list of violations. Each one has a
code, a path, the rejected value, the expected type and the parameters of its
message.

```php
use TypeLang\Mapper\Exception\InvalidValueException;

try {
    $mapper->denormalize(['id' => 'abc'], User::class);
} catch (InvalidValueException $e) {
    $violation = $e->violations[0];

    $violation->code;     // ViolationCode::InvalidType
    $violation->value;    // 'abc'
    $violation->declared; // TypeNode of `int`
    $violation->expected; // TypeNode of `int`
    $violation->cause;    // null, or the exception of a hook
}
```

The declared type is the type of the value as it is declared: by a property
or by the type given to the mapper. It is `null` when the declaration is not
known, such as for a generic type of your own. The expected type is the
declared one shown to a client: without the classes, enums and constants of
the application (see [messages](#messages)).

| Code             | Message                                                 |
|------------------|---------------------------------------------------------|
| `invalid_type`   | `Value must be of type {{expected}}, but {{value}} given`, `Value {{value}} is invalid` without the expected type |
| `invalid_key`    | `Key must be of type {{expected}}, but {{value}} given`, `Key {{value}} is invalid` without the expected type |
| `missing_field`  | `Required field "{{field}}" is missing`                 |
| `unexpected_field` | `Unexpected field "{{field}}"`                        |
| `duplicate_field` | `Field "{{field}}" duplicates the field "{{other}}"`, a property is given by its name and by an [alias](mapper-metadata-naming.md#field-aliases) |
| `failed_check`   | `Value must be of type {{expected}} ({{check}})`, `Value {{value}} is invalid ({{check}})` without the expected type |
| `invalid_value`  | `Value {{value}} is rejected`: an exception of a hook, or of a custom type nested in a list, an array, a shape or a class. Its message is not shown, the exception is `$violation->cause` and `$e->getPrevious()` |

## Paths

The path of a violation is printed as JSONPath. Properties renamed by
metadata have the names of the data.

```php
$mapper->denormalize(['items' => [['id' => 1], ['id' => 'x']]], Order::class);
// throws InvalidValueException: $.items[1].id: Value must be of type int, but
// "x" given
```

## Collecting Violations

By default the first violation fails. To collect several of them, use the
[max violations](mapper-configuration.md#max-violations) option.

```php
final class User
{
    public int $id;
    public string $name;
}

use TypeLang\Mapper\Configuration;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

$mapper = new Mapper(new Configuration(maxViolations: 100));

try {
    $mapper->denormalize(['id' => 'abc'], User::class);
} catch (InvalidValueException $e) {
    $e->getMessage();
    // '$.id: Value must be of type int, but "abc" given (and 1 more)'

    \count($e->violations); // 2
}
```

## Messages

Every message is formatted by the template of the code. Custom templates are
set by [metadata](mapper-metadata-errors.md).

The message of the exception is safe to return to clients: the classes,
enums and constants of the application are replaced by the data they take,
the messages of the exceptions of hooks are not shown.

```php
enum Status: string
{
    case New = 'new';
    case Paid = 'paid';
}

final class Address
{
    public string $city;
}

final class Order
{
    public Status $status;
    public ?Address $address;
}

$mapper->denormalize(['status' => 'x'], Order::class);
// throws InvalidValueException: $.status: Value must be of type 'new'|'paid',
// but "x" given

$mapper->denormalize(['status' => 'new', 'address' => 42], Order::class);
// throws InvalidValueException: $.address: Value must be of type
// ?object{city: string}, but 42 given
```

| Declared type                   | In the message                  |
|---------------------------------|---------------------------------|
| class                           | `object`, its fields with the [expected type depth](mapper-configuration.md#expected-type-depth): `object{name: string}` |
| interface, discriminator        | `object`                        |
| backed enum                     | values of the cases: `'new'\|'paid'` |
| unit enum                       | names of the cases: `'Hearts'\|'Spades'` |
| date                            | `string`                        |
| constant, mask of constants, `value-of<T>` | their values: `200\|201` |
| other types                     | as written: `list<'new'\|'paid'>`, `non-empty-string` |

An error of normalization is an error of the values of the application,
its message is written for a developer: with the declared type.

```php
$mapper->normalize('active', Status::class);
// throws InvalidValueException: $: Value must be of type App\Status, but
// "active" given
```

The formatters of both directions are arguments of the mapper:

```php
use TypeLang\Mapper\Diagnostics\Printer\Value\ShortValuePrinter;
use TypeLang\Mapper\Diagnostics\PublicViolationFormatter;
use TypeLang\Mapper\Diagnostics\ViolationFormatter;
use TypeLang\Printer\PrettyTypePrinter;

$printer = new PrettyTypePrinter();

$mapper = new Mapper(
    // the default ones
    denormalizationFormatter: new PublicViolationFormatter($printer),
    normalizationFormatter: new ViolationFormatter(
        new ShortValuePrinter(),
        $printer,
        shouldPrintDeclaredType: true,
    ),
);
```

The message shows `$violation->expected`, the type with the classes is
`$violation->declared`. To list the errors of an API response or to write a
message with the classes for a log, format the violations yourself:

```php
use TypeLang\Mapper\Configuration;
use TypeLang\Mapper\Diagnostics\Printer\Path\JsonPathPrinter;
use TypeLang\Mapper\Diagnostics\PublicViolationFormatter;
use TypeLang\Mapper\Diagnostics\ViolationFormatter;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

$mapper = new Mapper(new Configuration(maxViolations: 100));
$paths = new JsonPathPrinter();

try {
    $mapper->denormalize(['status' => 'x', 'address' => 42], Order::class);
} catch (InvalidValueException $e) {
    $errors = [];

    foreach ($e->violations as $violation) {
        $path = $paths->print($violation->path);
        $errors[$path] = (new PublicViolationFormatter())->format($violation);
    }

    $errors;
    // ['$.status' => 'Value must be of type \'new\'|\'paid\', but "x" given',
    // '$.address' => 'Value must be of type ?object{city: string}, but 42 given']

    (new ViolationFormatter(shouldPrintDeclaredType: true))->format($e->violations[0]);
    // 'Value must be of type \App\Status, but "x" given'
}
```

A type of your own is shown as it is written, unless an expander expands
it. A type wrapped by a decorator is expanded as it is built, unless an
expander supports the wrapper. An expander that throws leaves the message
without the type (`Value 42 is invalid`), the error of the expander is not
reported. `null` passes the type to the next expander:

```php
use TypeLang\Mapper\Context\ExpandContext;
use TypeLang\Mapper\Type\Expander\TypeExpanderInterface;
use TypeLang\Mapper\Type\TypeInterface;
use TypeLang\Type\Name;
use TypeLang\Type\NamedTypeNode;
use TypeLang\Type\TypeNode;

final class UuidTypeExpander implements TypeExpanderInterface
{
    public function tryExpand(TypeInterface $type, ExpandContext $context): ?TypeNode
    {
        if (!$type instanceof UuidType) {
            return null;
        }

        return new NamedTypeNode(new Name('string'));
    }
}

$mapper = new Mapper(expanders: [new UuidTypeExpander()]);
```

A type with nested types expands them by the context: `$context->expand()`
expands a nested type one level deeper. The fields over
`expectedTypeMaxFields` and at `expectedTypeDepth` are cut by the mapper for
any expansion.

```php
final class MoneyTypeExpander implements TypeExpanderInterface
{
    public function tryExpand(TypeInterface $type, ExpandContext $context): ?TypeNode
    {
        if (!$type instanceof MoneyType) {
            return null;
        }

        return new NamedTypeNode(new Name('object'), fields: new FieldListNode([
            new NamedFieldNode(new Identifier('amount'), new NamedTypeNode(new Name('int'))),
            new NamedFieldNode(new Identifier('currency'), $context->expand($type->currency)),
        ]));
        // object{amount: int, currency: 'EUR'|'USD'}
    }
}
```

<warning>
<code>Violation::$value</code> is the raw rejected value: do not log it as is
when the data may contain secrets. The messages of
<code>TypeDefinitionException</code> may contain paths of your files: do not
return them to clients.
</warning>
