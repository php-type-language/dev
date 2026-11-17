# Classes

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

A class name is a type like any other: `denormalize($data, Order::class)`
turns an array (or a `\stdClass`) into an `Order`, and `normalize($order)`
turns the object back into an array. No base class, interface or
configuration is required. This page describes which properties take part,
where their types come from and how the result can be tuned with
attributes.

```php
use TypeLang\Mapper\Mapper;

final class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency = 'EUR',
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount must not be negative');
        }
    }
}

final class Order
{
    public int $id;

    /** @var list<string> */
    public array $tags = [];

    public Money $total;

    private string $secret = 'internal';

    public static int $counter = 0;
}

$mapper = new Mapper();

$order = $mapper->denormalize([
    'id' => 42,
    'tags' => ['new', 'paid'],
    'total' => ['amount' => -100],
], Order::class);

var_dump($mapper->normalize($order));
```

```
array(3) {
  ["id"]=>
  int(42)
  ["tags"]=>
  array(2) {
    [0]=>
    string(3) "new"
    [1]=>
    string(4) "paid"
  }
  ["total"]=>
  array(2) {
    ["amount"]=>
    int(-100)
    ["currency"]=>
    string(3) "EUR"
  }
}
```

The example shows the three rules that matter most, each covered below:
only public non-static properties are mapped (`$secret` and `$counter` are
not), the object is created **without** calling its constructor (the
negative amount passes), and a missing field falls back to the default value
(`currency` is `EUR`).

## Mapped Properties

By default a class is mapped through its **public non-static** properties,
inherited ones included. Private, protected and static properties are
ignored in both directions: they are neither read from the data nor written
to the result. A private property keeps whatever value PHP gives it, its
declared default in the example above.

<tip>
The rule is a <code>PropertySelectorInterface</code> passed to
<code>MetadataProviderFactory::create(selector: ...)</code>; the default one
is <code>PublicPropertySelector</code>. See
<a href="mapper-metadata.md">Metadata</a>.
</tip>

Every mapped property must have a type: a native one, a PHPDoc one or one
from [`#[MapType]`](#property-types). An untyped `public $value;` makes the
whole class unusable:

```
Type of property "Person::$untyped" is not defined
```

### Objects Are Created Without the Constructor

On denormalization the object is created without calling its constructor,
then each property is written directly. This is what lets the mapper fill
`readonly` and promoted properties, but it also means:

- **Constructor checks do not run.** Any invariant enforced in
  `__construct()` (like the non-negative amount above) is bypassed. Validate
  the data with a precise type instead (`int<0, max>`, `non-empty-string`,
  ...), or with a hook.
- **Declared defaults still apply.** A property with a default value
  (`public array $tags = [];`) has it before the mapper writes anything.

`readonly` properties and promoted constructor properties are mapped like
any other public property: in the example `Money::$amount` is both.

<note>
On PHP 8.4 a property with a backing store and a <code>set</code> or
<code>get</code> hook is written and read <b>bypassing the hook</b>, so a
validating <code>set</code> hook does not run either. A <i>virtual</i>
property (one with hooks and no backing store) goes through its hooks: it is
normalized only if it has a <code>get</code> hook and denormalized only if it
has a <code>set</code> hook. Its type in each direction comes from that hook
(the <code>@return</code> of <code>get</code>, the <code>@param</code> of
<code>set</code>, or a <code>#[MapType]</code> written on the hook).
</note>

To create objects another way, for example through the constructor or a
factory, or to run checks after an object is filled, see
[Extending](mapper-extending.md).

## Property Types

The type of a property is taken from the first of these sources that has
one:

1. the `#[MapType]` attribute;
2. a `@type-lang-var` tag of the property;
3. a PHPDoc `@var` tag of the property (`@phpstan-var`, `@psalm-var` and
   `@phan-var` are read as well), or, for a promoted property, the `@param`
   tag of the constructor;
4. the native type of the property.

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MapType;

final class Report
{
    /**
     * @var list<int>
     */
    public array $scores;

    /**
     * @type-lang-var list<positive-int>
     * @phpstan-var list<int>
     */
    public array $pages;

    /**
     * @var list<int>
     */
    #[MapType('list<numeric-string>')]
    public array $codes;

    /**
     * @param non-empty-string $title
     */
    public function __construct(
        public readonly string $title,
    ) {}
}
```

Here `$scores` is a `list<int>` from `@var`, `$pages` a `list<positive-int>`
(`@type-lang-var` wins over `@phpstan-var`), `$codes` a
`list<numeric-string>` (`#[MapType]` wins over `@var`), and `$title` a
`non-empty-string` from the constructor's `@param`. A value that does not
fit the chosen type is reported with its path:

```
$.pages[0]: Value must be of type "positive-int" (>= 1)
$.codes[0]: Value must be of type "numeric-string" (numeric)
$.title: Value must be of type "non-empty-string" (non-empty)
```

<tip>
<code>@type-lang-var</code> (with <code>@type-lang-param</code> and
<code>@type-lang-return</code>) is for a type that only the mapper should
see: write it when a static analyzer cannot understand the type, or when the
mapper needs a different type than the one written for the analyzer.
</tip>

Names of classes in `#[MapType]` and in PHPDoc tags are resolved by the
`use` imports and the namespace of the file the property is declared in, the
same way PHP resolves them:

```php
#[MapType('list<Address>')]
public array $addresses;
```

See [Type Language](basic-types.md) for the syntax and
[Types](mapper-types.md) for the types the mapper supports.

<note>
PHPDoc types are read only when the <code>type-lang/phpdoc</code> package is
installed (<code>composer require type-lang/phpdoc</code>). Without it the
tags are ignored and the native type (<code>array</code> above) is used.
</note>

### Strict Properties

`#[MapType]` also takes a `strict` flag that disables
[coercion](mapper-coercion.md) for this one property, whatever the mapper
configuration says:

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\MapName;
use TypeLang\Mapper\Mapping\MapType;

final class Customer
{
    #[MapName('customer_id')]
    #[MapType('int', strict: true)]
    public int $id;

    public int $visits = 0;
}

$mapper = new Mapper();

$customer = $mapper->denormalize(['customer_id' => 7, 'visits' => '3'], Customer::class);

var_dump($mapper->normalize($customer));

try {
    $mapper->denormalize(['customer_id' => '7'], Customer::class);
} catch (\Exception $e) {
    echo $e->getMessage(), "\n";
}
```

```
array(2) {
  ["customer_id"]=>
  int(7)
  ["visits"]=>
  int(3)
}
$.customer_id: Value must be of type "int", but "7" given
```

`visits` still accepts the string `'3'`, `customer_id` does not.

## Field Names

A property is read from and written to the field with the same name. The
`#[MapName]` attribute gives it another name, used in both directions and in
error paths (`$.customer_id` above). To rename all properties of a class or
of the whole mapper by a rule, such as `snake_case`, use a
[naming strategy](mapper-naming.md); an explicit `#[MapName]` always wins
over it.

## Missing Fields and Default Values

A field missing from the data is filled with the default value of the
property, or, for a promoted property, with the default value of its
constructor parameter. A property without a default value is required.

A missing field and a field set to `null` are different things: `null` is a
value and must fit the type, while a missing field is only allowed when
there is a default.

```php
use TypeLang\Mapper\Mapper;

final class Profile
{
    public ?string $nickname;

    public ?string $bio = null;

    public function __construct(
        public readonly string $name,
        public readonly string $locale = 'en',
    ) {}
}

$mapper = new Mapper();

var_dump($mapper->denormalize(['name' => 'Ada', 'nickname' => null], Profile::class));

foreach ([['name' => 'Ada'], ['name' => 'Ada', 'nickname' => null, 'locale' => null]] as $data) {
    try {
        $mapper->denormalize($data, Profile::class);
    } catch (\Exception $e) {
        echo $e->getMessage(), "\n";
    }
}
```

```
object(Profile)#896 (4) {
  ["nickname"]=>
  NULL
  ["bio"]=>
  NULL
  ["name"]=>
  string(3) "Ada"
  ["locale"]=>
  string(2) "en"
}
$.nickname: Required field "nickname" is missing
$.locale: Value must be of type "string", but null given
```

- `$nickname` is nullable but has no default, so it is required: `null` is
  accepted, a missing field is not.
- `$bio` has a default, so it may be missing.
- `$locale` may be missing (its constructor parameter has a default), but
  `null` does not fit `string`.

A default value may be `null`, a scalar, an enum case or an array of them.

Fields of the data that no property takes are dropped silently; to reject
them, see [extra fields](mapper-errors.md).

## Skipping Properties on Normalization

Three attributes leave a property out of the normalized data. They have no
effect on denormalization.

| Attribute | The property is left out when |
|---|---|
| `#[SkipWhenNull]` | its value is `null` |
| `#[SkipWhenEmpty]` | its value is empty by the rules of `empty()`: `null`, `false`, `0`, `0.0`, `''`, `'0'` or `[]` |
| `#[SkipWhen('expression')]` | the expression is true |

A property may have several conditions; any one of them is enough.

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\SkipWhen;
use TypeLang\Mapper\Mapping\SkipWhenEmpty;
use TypeLang\Mapper\Mapping\SkipWhenNull;

final class User
{
    public string $name = '';

    #[SkipWhenNull]
    public ?string $phone = null;

    /** @var list<string> */
    #[SkipWhenEmpty]
    public array $roles = [];

    #[SkipWhen('this.isHidden')]
    public string $email = '';

    #[SkipWhen('user.age < 18 or user.isGuest()', context: 'user')]
    public ?int $age = null;

    public bool $isHidden = false;

    public function isGuest(): bool
    {
        return $this->name === '';
    }
}

$mapper = new Mapper();

$user = new User();
$user->name = 'Ada';
$user->email = 'ada@example.com';
$user->age = 36;

var_dump($mapper->normalize($user));

$user->isHidden = true;
$user->age = 17;
$user->roles = ['admin'];
$user->phone = '+1 555';

var_dump($mapper->normalize($user));
```

```
array(4) {
  ["name"]=>
  string(3) "Ada"
  ["email"]=>
  string(15) "ada@example.com"
  ["age"]=>
  int(36)
  ["isHidden"]=>
  bool(false)
}
array(4) {
  ["name"]=>
  string(3) "Ada"
  ["phone"]=>
  string(6) "+1 555"
  ["roles"]=>
  array(1) {
    [0]=>
    string(5) "admin"
  }
  ["isHidden"]=>
  bool(true)
}
```

### Expressions

`#[SkipWhen]` takes an expression of the
[Symfony ExpressionLanguage](https://symfony.com/doc/current/components/expression_language.html)
and requires the package:

```bash
composer require symfony/expression-language
```

The object being normalized is available as `this`; the second argument,
`context`, gives it another name (`user` above). The value of the property
itself is not a separate variable: read it through the object
(`this.email`).

The expression is parsed once, when the metadata of the class is read, so a
syntax error makes the class unusable before any data is mapped:

```
Type "Bad" cannot be built: Cannot read metadata of "Bad":
  - Bad::$value: expression "this." is invalid (Unexpected end of expression around position 6 for expression `this.`.) in /path/to/Bad.php
```

<warning>
Only variables, properties, method calls of the object and operators are
supported. Expression functions, such as <code>constant()</code>, are
accepted by the parser but fail when the expression is evaluated.
</warning>

## Arrays or Objects on Normalization

A normalized object is a PHP array by default. With
`Configuration::$isObjectAsArray` set to `false` every object becomes a
`\stdClass` instead. The `#[NormalizeAsArray]` attribute overrides the
configuration for one class: `#[NormalizeAsArray(false)]` gives a
`\stdClass`, `#[NormalizeAsArray]` an array.

The difference shows up when the result is encoded to JSON: an empty
`\stdClass` is encoded as `{}`, an empty array as `[]`.

```php
use TypeLang\Mapper\Context\Configuration;
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\NormalizeAsArray;
use TypeLang\Mapper\Mapping\SkipWhenNull;

final class Theme
{
    #[SkipWhenNull]
    public ?string $color = null;
}

#[NormalizeAsArray(false)]
final class Settings
{
    #[SkipWhenNull]
    public ?string $locale = null;
}

final class Page
{
    public Theme $theme;

    public Settings $settings;
}

$page = new Page();
$page->theme = new Theme();
$page->settings = new Settings();

echo \json_encode((new Mapper())->normalize($page)), "\n";

$mapper = new Mapper(new Configuration(isObjectAsArray: false));

echo \json_encode($mapper->normalize($page)), "\n";
```

```
{"theme":[],"settings":{}}
{"theme":{},"settings":{}}
```

See [Configuration](mapper-configuration.md) for the other settings.

## Nested and Recursive Classes

A property may have any type the mapper supports, including another class,
a list of classes or the class itself. Recursive structures such as trees
need nothing special:

```php
use TypeLang\Mapper\Mapper;

final class Node
{
    public string $name;

    /** @var list<Node> */
    public array $children = [];
}

$mapper = new Mapper();

$tree = $mapper->denormalize([
    'name' => 'root',
    'children' => [
        ['name' => 'a', 'children' => [['name' => 'a.1']]],
        ['name' => 'b'],
    ],
], Node::class);

echo $tree->children[0]->children[0]->name, "\n";

echo \json_encode($mapper->normalize($tree)), "\n";
```

```
a.1
{"name":"root","children":[{"name":"a","children":[{"name":"a.1","children":[]}]},{"name":"b","children":[]}]}
```

<warning>
Normalization does not track the objects it has already visited. An object
graph with a cycle (a child holding a public reference back to its parent)
recurses until PHP runs out of memory. Keep back references out of the
mapping, for example by making them non-public.
</warning>

For a property whose class depends on the data (a `Shape` that is a
`Circle` or a `Square`), see [Polymorphism](mapper-polymorphism.md).

## Where to Go Next

<deflist>
    <def title="Error messages">
        Custom messages for a property or a class with
        <code>#[OnTypeError]</code> and <code>#[OnUndefinedError]</code>. See
        <a href="mapper-errors.md">Errors</a>.
    </def>
    <def title="Metadata">
        The same settings in YAML, JSON, NEON or PHP files, and caching. See
        <a href="mapper-metadata.md">Metadata</a>.
    </def>
    <def title="Extending">
        Hooks around the conversion of a class, custom instantiation and
        property access. See <a href="mapper-extending.md">Extending</a>.
    </def>
</deflist>
