# Errors

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

A conversion fails for one of two reasons, and the mapper throws a
different exception for each:

- `InvalidValueException`: the **data** does not match the type. This is
  an expected outcome for input from outside, such as a request body, and
  the exception describes every problem with the path to the wrong value.
- `TypeDefinitionException`: the **type** is wrong: unknown, written with a
  syntax error, or declared wrong in a class. This is a bug of the code,
  it does not depend on the data.

Both live in `TypeLang\Mapper\Exception` and implement
`MapperExceptionInterface`, so a single `catch` handles any failure of the
mapper:

```php
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Exception\MapperExceptionInterface;
use TypeLang\Mapper\Mapper;

$mapper = new Mapper();

foreach ([[[1, 2, 3], 'list<int>'], [[1, 2, 'x'], 'list<int>'], [[1, 2, 3], 'list<int']] as [$data, $type]) {
    try {
        echo \json_encode($mapper->denormalize($data, $type)), "\n";
    } catch (InvalidValueException $e) {
        // the data is invalid: reject the input
        echo 'Invalid data: ', $e->getMessage(), "\n";
    } catch (MapperExceptionInterface $e) {
        // the type is invalid: fix the code
        echo 'Invalid type: ', $e->getMessage(), "\n";
    }
}
```

```
[1,2,3]
Invalid data: $[2]: Value must be of type "int", but "x" given
Invalid type: Type "list<int" is written wrong: Invalid template arguments, "," or ">" is expected
```

## Invalid Data

`InvalidValueException` carries the list of problems in its public
`violations` property, in the order they were found. Its message is the
first violation with its path, followed by the number of the others:
`$.path: text (and N more)`.

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MapType;

final class Item
{
    public int $id;
    public string $title;
}

final class Order
{
    public string $number;

    #[MapType('list<Item>')]
    public array $items;
}

$mapper = new Mapper(new Configuration(maxViolations: 10));

$data = [
    'items' => [
        ['id' => 1, 'title' => 'Pen'],
        ['id' => 'two', 'title' => 'Book'],
        ['id' => 3],
    ],
];

try {
    $mapper->denormalize($data, Order::class);
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n\n";

    foreach ($e->violations as $violation) {
        echo $violation->code->getCode(), ' ';
        echo \json_encode($violation->parameters), ' ';
        echo \var_export($violation->value, true), "\n";
    }
}
```

```
$.number: Required field "number" is missing (and 2 more)

missing_field {"field":"number"} NULL
invalid_type {"expected":"int"} 'two'
missing_field {"field":"title"} NULL
```

By default the first violation stops the conversion. The example above
raises `maxViolations` to collect several, see
[Collecting Violations](mapper-configuration.md#collecting-violations).

Each `TypeLang\Mapper\Diagnostics\Violation` is a read-only value:

| Property     | Content                                                                                               |
|--------------|-------------------------------------------------------------------------------------------------------|
| `code`       | Kind of the violation, a `ViolationCodeInterface`: `getCode()` gives a stable string such as `invalid_type`. |
| `parameters` | Values of the placeholders of the message, such as `expected` or `field`.                            |
| `path`       | `Path` from the root of the data to the wrong value, a list of `segments`.                            |
| `value`      | The rejected value as it came in, `null` for a missing field.                                         |
| `template`   | Message template set by the user, `null` when the template of the code is used.                       |

### Paths

The path of a violation is printed as [JSONPath](https://www.rfc-editor.org/rfc/rfc9535):
`$` is the root of the data, `.name` a field, `[0]` an element of a list.
A key that is not a plain identifier is quoted and escaped:

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Diagnostics\Printer\Path\JsonPathPrinter;
use TypeLang\Mapper\Diagnostics\Printer\Value\ShortValuePrinter;
use TypeLang\Mapper\Diagnostics\ViolationFormatter;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

$mapper = new Mapper(new Configuration(maxViolations: 10));

$paths = new JsonPathPrinter();
$formatter = new ViolationFormatter(new ShortValuePrinter(maxStringLength: 0));

try {
    $mapper->denormalize([
        'scores' => ['alice' => 10, 'bob smith' => 'ten', "it's" => 'x'],
        'tags' => ['ok', '', 'secret-token-1234'],
    ], 'array{scores: array<string, int>, tags: list<non-empty-string>}');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n\n";

    foreach ($e->violations as $violation) {
        echo $paths->print($violation->path), ': ', $formatter->format($violation), "\n";
    }
}
```

```
$.scores['bob smith']: Value must be of type "int", but "ten" given (and 2 more)

$.scores['bob smith']: Value must be of type "int", but string(3) given
$.scores['it\'s']: Value must be of type "int", but string(1) given
$.tags[1]: Value must be of type "non-empty-string" (non-empty)
```

The path names fields as they are written in the data, after a
[naming strategy](mapper-naming.md) if there is one: a property
`$firstName` mapped as `first_name` fails at `$.first_name`. Normalization
errors use the same names.

### Violation Codes

The built-in kinds are the cases of the `TypeLang\Mapper\Diagnostics\ViolationCode`
enum. Each has a default message template, `{{value}}` in it is the
rejected value:

| Case               | Code               | Default template                                            |
|--------------------|--------------------|-------------------------------------------------------------|
| `InvalidType`      | `invalid_type`     | `Value must be of type "{{expected}}", but {{value}} given` |
| `InvalidKey`       | `invalid_key`      | `Key must be of type "{{expected}}", but {{value}} given`   |
| `MissingField`     | `missing_field`    | `Required field "{{field}}" is missing`                     |
| `UnexpectedField`  | `unexpected_field` | `Unexpected field "{{field}}"`                              |
| `FailedCheck`      | `failed_check`     | `Value must be of type "{{expected}}" ({{check}})`          |
| `InvalidValue`     | `invalid_value`    | `{{message}}`                                               |

- `invalid_type` is also the code of a union none of whose branches
  matched, `expected` is then the whole union.
- `failed_check` is a value of the right type that fails a refinement,
  such as `''` of `non-empty-string` or `0` of `positive-int`.
- The path of `missing_field` and `unexpected_field` points to the field
  itself.
- `invalid_value` is an exception of another kind thrown during the
  conversion, such as by a [hook](mapper-extending.md), its message
  becomes `{{message}}` and the exception itself is kept as the previous
  one:

```php
use TypeLang\Mapper\Context\Context;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Hook\AfterDenormalizationHookInterface;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\Metadata\ClassMetadata;

final class Period
{
    public int $from;
    public int $to;
}

final class PeriodCheck implements AfterDenormalizationHookInterface
{
    public function isSupported(ClassMetadata $metadata): bool
    {
        return $metadata->name === Period::class;
    }

    public function afterDenormalize(ClassMetadata $metadata, object $object, mixed $data, Context $context): void
    {
        if ($object->from > $object->to) {
            throw new \DomainException('The period ends before it starts');
        }
    }
}

$mapper = new Mapper(hooks: [new PeriodCheck()]);

var_dump($mapper->isDenormalizable(['from' => 5, 'to' => 1], Period::class));

try {
    $mapper->denormalize(['period' => ['from' => 5, 'to' => 1]], 'array{period: Period}');
} catch (InvalidValueException $e) {
    echo $e->getMessage(), "\n";
    echo $e->violations[0]->code->getCode(), "\n";
    echo \get_class($e->getPrevious()), "\n";
}
```

```
bool(true)
$.period: The period ends before it starts
invalid_value
DomainException
```

Note the `true` of `isDenormalizable()`: a check creates no object and
therefore does not run the hooks that run after an object is filled.

Extensions may declare their own kinds by implementing
`ViolationCodeInterface` (`getCode()` and `getTemplate()`), usually as an
enum.

## Formatting Violations

The message of the exception is meant for a developer. To build your own
output, such as a list of errors of a form, format each violation with the
public classes of `TypeLang\Mapper\Diagnostics`:

- `ViolationFormatter` renders the template of a violation with its
  parameters and its value. Unknown placeholders are left as they are.
  Implement `ViolationFormatterInterface` to render it differently, such as
  through a translator keyed by `$violation->code->getCode()`.
- `JsonPathPrinter` prints a `Path` as JSONPath, as shown above.
  `PathPrinterInterface` is there for another notation.
- `ShortValuePrinter` prints the value for `{{value}}`: scalars by value,
  arrays and objects by kind. Strings longer than `maxStringLength`
  characters (40 by default) are cut, `0` hides their contents and prints
  only their length in bytes, as in the path example above.

```php
use TypeLang\Mapper\Diagnostics\Printer\Value\ShortValuePrinter;

$printer = new ShortValuePrinter();

foreach ([null, true, 1.0, 'text', \str_repeat('a', 50), [1, 2, 3], new \ArrayObject()] as $value) {
    echo $printer->print($value), "\n";
}
```

```
null
true
1.0
"text"
"aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa"...
array(3)
object(ArrayObject)
```

## Custom Messages

The `#[OnTypeError]` and `#[OnUndefinedError]` attributes of
`TypeLang\Mapper\Mapping` replace the default messages with your own text.
Placeholders of the violation are replaced in it: `{{value}}`,
`{{expected}}`, `{{field}}` and the others of the table above.

- `#[OnTypeError]` on a property: the message of an invalid value of the
  property.
- `#[OnTypeError]` on a class: the message of a value that is not an
  object of the class at all, such as a string instead of an array.
- `#[OnUndefinedError]` on a property: the message of the missing field.

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\OnTypeError;
use TypeLang\Mapper\Mapping\OnUndefinedError;

#[OnTypeError('A person is expected, {{value}} given')]
final class Person
{
    #[OnTypeError('Age must be a number, {{value}} given')]
    public int $age;

    #[OnUndefinedError('Field {{field}} is required')]
    public string $name;
}

final class Team
{
    #[OnTypeError('The lead is invalid')]
    public Person $lead;
}

$mapper = new Mapper(new Configuration(maxViolations: 10));

$cases = [
    [Person::class, ['age' => 'x', 'name' => 'Alice']],
    [Person::class, ['age' => 42]],
    [Person::class, 'Alice'],
    [Team::class, ['lead' => 'Alice']],
    [Team::class, ['lead' => ['age' => 'x']]],
];

foreach ($cases as [$type, $data]) {
    try {
        $mapper->denormalize($data, $type);
    } catch (InvalidValueException $e) {
        echo $e->getMessage(), "\n";
    }
}
```

```
$.age: Age must be a number, "x" given
$.name: Field name is required
$: A person is expected, "Alice" given
$.lead: The lead is invalid
$.lead.age: Age must be a number, "x" given (and 1 more)
```

The last two lines show the scope of a property message: it replaces the
messages of the property's own value only. `Team::$lead` set to a string
fails with `The lead is invalid`, while the violations inside a valid
`Person` array keep their own messages.

The message is changed, the violation is not: its code, parameters, path
and value stay the same, so code that reads `violations` is not affected
by custom texts.

## Returning Errors from an API

Collect all violations, reject unknown fields and turn the result into a
list of `{path, message}` objects:

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Diagnostics\Printer\Path\JsonPathPrinter;
use TypeLang\Mapper\Diagnostics\ViolationFormatter;
use TypeLang\Mapper\Exception\InvalidValueException;
use TypeLang\Mapper\Mapper;

final class CreateUser
{
    public string $email;
    public int $age;
}

$mapper = new Mapper(new Configuration(maxViolations: 50, shouldRejectExtraFields: true));

$paths = new JsonPathPrinter();
$formatter = new ViolationFormatter();

try {
    $mapper->denormalize(['age' => 'old', 'e-mail' => 'a@example.com'], CreateUser::class);
} catch (InvalidValueException $e) {
    $errors = [];

    foreach ($e->violations as $violation) {
        $errors[] = [
            'path' => $paths->print($violation->path),
            'code' => $violation->code->getCode(),
            'message' => $formatter->format($violation),
        ];
    }

    echo \json_encode(['errors' => $errors], \JSON_PRETTY_PRINT), "\n";
}
```

```
{
    "errors": [
        {
            "path": "$.email",
            "code": "missing_field",
            "message": "Required field \"email\" is missing"
        },
        {
            "path": "$.age",
            "code": "invalid_type",
            "message": "Value must be of type \"int\", but \"old\" given"
        },
        {
            "path": "$['e-mail']",
            "code": "unexpected_field",
            "message": "Unexpected field \"e-mail\""
        }
    ]
}
```

`maxViolations` limits the work an invalid request can cause, choose it
rather than an unlimited number. The options are described in
[Configuration](mapper-configuration.md).

<warning>
<p><code>Violation::$value</code> holds the rejected input as it came in,
such as a password or a token sent in the wrong field. A logger or an error
tracker that serializes the exception writes it out. Log the path and the
code, not the whole exception, and format messages with
<code>new ShortValuePrinter(maxStringLength: 0)</code> where the value must
not be shown.</p>
<p>The <code>{{message}}</code> of <code>invalid_value</code> is the message
of an exception thrown by your code, check that it is fit for a client
before returning it.</p>
</warning>

## Invalid Types

`TypeDefinitionException` (a `\LogicException`) reports a type that cannot
be built, whatever the data:

```php
use TypeLang\Mapper\Exception\TypeDefinitionException;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MapType;

final class Broken
{
    #[MapType('list<int')]
    public array $ids;
}

final class Untyped
{
    public $anything;
}

$mapper = new Mapper();

foreach (['list<int', 'App\\Missing', 'int<10, 1>', Broken::class, Untyped::class] as $type) {
    try {
        $mapper->denormalize([], $type);
    } catch (TypeDefinitionException $e) {
        echo $e->getMessage(), "\n";
    }
}
```

```
Type "list<int" is written wrong: Invalid template arguments, "," or ">" is expected
Type "App\Missing" is not supported
Type "int<10, 1>" is empty: the lower bound is greater than the upper one
Type "Broken" cannot be built: Cannot read metadata of "Broken":
  - Broken::$ids: Invalid template arguments, "," or ">" is expected in C:\...\example.php
Type of property "Untyped::$anything" is not defined
```

- **Written wrong**: a syntax error in a type string; the error of the
  [parser](parser.md) is the previous exception.
- **Not supported**: no type of the mapper takes the name, such as a class
  that does not exist or a name the [types](mapper-types.md) do not know.
  Class names in a type string are fully qualified.
- **Declared wrong**: the type parses but makes no sense, such as an empty
  range, or the [metadata](mapper-metadata.md) of a class is invalid: a
  property type written wrong in an attribute, PHPDoc or a configuration
  file, a property without any type, a wrong discriminator map.

The predicates `isDenormalizable()` and `isNormalizable()` throw it too:
a broken type is not an answer of "no".

Write type strings as constant strings to let the
[PHPStan extension](mapper-phpstan.md) report syntax errors before the code
runs.

<warning>
Messages of <code>TypeDefinitionException</code> may contain class names and
paths of your source files. They are meant for developers and logs, do not
return them to clients.
</warning>
