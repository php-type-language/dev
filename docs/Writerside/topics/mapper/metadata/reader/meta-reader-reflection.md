# Reflection Reader

<link-summary>
Reads metadata from the PHP class definition.
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Mapping\Reader\ReflectionReader</code>
    </p>
</tldr>

This reader reads **native** class information, specifically:
- All public (for read) non-static properties
  - Property read type
  - Property write type (If the property implements [a set hook](https://www.php.net/manual/en/language.oop5.property-hooks.php))
  - A default value
    - The default argument passed to the promoted property

By default, this reader receives basic information from 
[the `NullReader` reader](meta-reader-null.md) and supplements it. If you need 
to supplement metadata from another reader, you should 
specify this explicitly using `$delegate` argument:

```php
use TypeLang\Mapper\Mapping\Reader\ReflectionReader;

$reader = new ReflectionReader(
    delegate: new AnotherExampleReader(),
);
```

## Class Properties

As was written earlier, only public and non-static properties will be read.

This behavior allows you to provide access only to those properties that can 
be read from the object directly by the user from PHP code, while maintaining 
encapsulation (not providing information about properties that are not available
to the user).

For example, the following properties (marked by `✔`) will be known 
in the result metadata:

```php
final class ExampleClass
{
    public mixed $a;                        // ✔
    protected mixed $b;                     // ⨉
    private mixed $c;                       // ⨉

    public protected(set) mixed $d;         // ✔ (PHP >= 8.4)
    public private(set) mixed $e;           // ✔ (PHP >= 8.4)
    protected private(set) mixed $f;        // ⨉ (PHP >= 8.4)

    public static mixed $g;                 // ⨉
    protected static mixed $h;              // ⨉
    private static mixed $i;                // ⨉

    public protected(set) static mixed $j;  // ⨉ (PHP >= 8.5)
    public private(set) static mixed $k;    // ⨉ (PHP >= 8.5)
    protected private(set) static mixed $l; // ⨉ (PHP >= 8.5)
}
```

<tip>
The logic for reading <a href="https://php.watch/versions/8.0/constructor-property-promotion">
promoted properties</a> are similar.
</tip>


## Property Types

The logic for reading types is quite simple. For example, let's define 
three properties:

```php
final class ExampleClass
{
    public string $a;   // string
    public mixed $b;    // mixed
    public $c;          // mixed
}
```

- For property `$a` the type `string` (read+write) will be inferred
- For property `$b` the type `mixed` (read+write) will be inferred
- For property `$c` the type `mixed` (read+write) will be inferred

When using "property hooks", the types for writing and reading may differ, so
when writing the example below, the types will be different:

```php
final class ExampleClass
{
    public string $a {              // read:  string
        set(string|int $v) => ...   // write: string|int
    }
}
```

- The `string` type will be inferred for **reading**
- The `string|int` type will be inferred for **writing**

## Property Default Value

The default value is inferred from the default value of the class property.

```php
final class ExampleClass
{
    public string $a = 'default';       // default: string("default")
    public mixed $b;                    // default: ~
    public $c;                          // default: null

    public function __construct(
        public mixed $d,                // default: ~
        public ?string $e,              // default: ~
        public string $f = 'default',   // default: string("default")
    ) {}
}
```

- Property `$a` contains default value `"default"`
- Property `$b` does not contain default value (although it accepts `mixed` type)
- Property `$c` contains default value `null`
- Promoted property `$d` does not contain default value (although it accepts `mixed` type)
- Promoted property `$e` does not contain default value (although it nullable)
- Property `$f` contains default value `"default"` (although technically the 
  default value is not for the property, but for the constructor's argument)

<note>
Note that any other constructor arguments (non-promoted) <b>do not</b> matter.

<code-block lang="PHP">
final class ExampleClass
{
    public string $name;    // default: ~

    public function __construct(
        string $name = 'default',
    ) {
        $this->name = $name;
    }
}
</code-block>

The property <code>$name</code> will <b>not have</b> a default value.
</note>



