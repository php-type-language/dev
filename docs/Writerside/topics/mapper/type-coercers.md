# Type Coercers

Type coercers (type casters) are a set of classes that convert incoming values 
into a value of a specific type.

<note>
Type coercers automatically disabled when using the 
<code>strictTypes: true</code> configuration setting
</note>

A set of coercers is necessary if you're using an external environment that
doesn't support a specific PHP type. For example, when working with HTTP with, 
you can only accept strings. However, to convert the HTTP body (or query 
arguments) to a specific DTO, string values must be coerced to specific types.

For example, we have a DTO:

```php
final readonly class UserInfoRequestDTO
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
```

And the following request:

```http
POST /example
Content-Type: application/x-www-form-urlencoded

id=42&name=Kirill
```

PHP will receive the following values:

```php
var_dump($_POST);

// array(2) {
//   'id' => '42',
//   'name' => 'Kirill',
// }
```

Thus, when trying to map values directly, without type coercions, 
we will get an error:

```php
$dto = $mapper->denormalize($_POST, UserInfoRequestDTO::class);

// InvalidValueException: Passed value "42" is invalid at $.id
```

In case of using int type coercer, the string `"42"` will be automatically 
converted into the required `42` integer value.

```php
$dto = $mapper->denormalize($_POST, UserInfoRequestDTO::class);

// object(UserInfoRequestDTO) {
//    id: int(42),
//    name: string("Kirill")
// }
```

<note>
The built-in type coercers are designed to perform only safe casts
without loss of accuracy or meaning.
</note>

<tip>
To <b>create</b> custom type coercer, see the 
<a href="custom-type-coercer.md">"custom type coercer" documentation page</a>.
</tip>
<tip>
To <b>register</b> a coercer, see the 
<a href="custom-platform.md#adding-type-coercions">"custom platform" documentation page</a>.
</tip>