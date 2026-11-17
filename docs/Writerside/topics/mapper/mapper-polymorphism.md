# Polymorphism

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

A property typed as an abstract class or an interface, such as
`list<Shape>`, cannot be denormalized on its own: the data has to say which
concrete class each item is. The `#[DiscriminatorMap]` attribute declares
that: one field of the data holds a **tag**, and the map says which class
each tag stands for.

```php
namespace App\Geometry;

use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\DiscriminatorMap;

#[DiscriminatorMap(field: 'type', map: [
    'circle' => 'Circle',
    'square' => 'Square',
])]
abstract class Shape {}

final class Circle extends Shape
{
    public float $radius;
}

final class Square extends Shape
{
    public float $side;
}

final class Drawing
{
    /** @var list<Shape> */
    public array $shapes = [];
}

$mapper = new Mapper();

$drawing = $mapper->denormalize(['shapes' => [
    ['type' => 'circle', 'radius' => 1.5],
    ['type' => 'square', 'side' => 2],
]], Drawing::class);

var_dump($drawing->shapes);

echo \json_encode($mapper->normalize($drawing)), "\n";
```

```
array(2) {
  [0]=>
  object(App\Geometry\Circle)#898 (1) {
    ["radius"]=>
    float(1.5)
  }
  [1]=>
  object(App\Geometry\Square)#897 (1) {
    ["side"]=>
    float(2)
  }
}
{"shapes":[{"type":"circle","radius":1.5},{"type":"square","side":2}]}
```

## Declaring the Map

`#[DiscriminatorMap]` is written on the base class or interface and takes:

- `field` - the key of the data holding the tag, used as written
  ([naming strategies](mapper-naming.md) do not rename it);
- `map` - the tags (strings or integers) and the classes they select;
- `otherwise` - optional, the class for data with a missing or unknown tag,
  see [below](#fallback-for-unknown-tags).

Class names in `map` and `otherwise` are resolved by the `use` imports and
the namespace of the file the attribute is written in: `'Circle'` above
means `App\Geometry\Circle`. Writing `Circle::class` works just as well.

Every class of the map must be the base class itself or extend (implement)
it. The base may be an interface:

```php
namespace App\Payment;

use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\DiscriminatorMap;

#[DiscriminatorMap(field: 'method', map: [
    'card' => CardPayment::class,
    'cash' => CashPayment::class,
])]
interface Payment {}

final class CardPayment implements Payment
{
    public string $last4;
}

final class CashPayment implements Payment
{
    public int $amount;
}

$mapper = new Mapper();

$payment = $mapper->denormalize(['method' => 'cash', 'amount' => 100], Payment::class);

echo $payment::class, "\n";
```

```
App\Payment\CashPayment
```

The base class may also appear in its own map when it is not abstract, as
`Event` does in the [example below](#fallback-for-unknown-tags).

## Denormalization

The mapper reads the tag first, picks the class and denormalizes the whole
data by that class. The tag field itself is not written to any property,
unless the selected class has a property with that name.

Data without the tag, or with a tag that is not in the map, is a violation
reported at the path of the tag field:

```php
use TypeLang\Mapper\Exception\InvalidValueException;

foreach ([['radius' => 1], ['type' => 'triangle'], ['type' => null]] as $data) {
    try {
        $mapper->denormalize($data, Shape::class);
    } catch (InvalidValueException $e) {
        echo $e->getMessage(), "\n";
    }
}
```

```
$.type: Required field "type" is missing
$.type: Value must be of type "'circle'|'square'", but "triangle" given
$.type: Value must be of type "'circle'|'square'", but null given
```

When extra fields are rejected
(`Configuration::$shouldRejectExtraFields`), the tag field is never counted
as one, even though no property takes it. See [Errors](mapper-errors.md).

### Fallback for Unknown Tags

With `otherwise`, data whose tag is missing or unknown is denormalized by
that class instead of failing:

```php
namespace App\Events;

use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\DiscriminatorMap;

#[DiscriminatorMap(
    field: 'kind',
    map: [
        'event' => Event::class,
        'login' => LoginEvent::class,
        'custom' => CustomEvent::class,
    ],
    otherwise: Event::class,
)]
class Event
{
    public string $id = '';
}

class LoginEvent extends Event
{
    public string $user = '';
}

final class CustomEvent extends Event
{
    public string $kind = 'custom:payment';
}

$mapper = new Mapper();

foreach ([
    ['kind' => 'login', 'id' => '1', 'user' => 'ada'],
    ['kind' => 'event', 'id' => '2'],
    ['kind' => 'unknown', 'id' => '3'],
    ['id' => '4'],
] as $data) {
    echo \get_class($mapper->denormalize($data, Event::class)), "\n";
}
```

```
App\Events\LoginEvent
App\Events\Event
App\Events\Event
App\Events\Event
```

## Normalization

On normalization the class of the object selects the tag, and the tag is
written as the **first** key of the result, before the properties:

```
{"shapes":[{"type":"circle","radius":1.5},{"type":"square","side":2}]}
```

Two cases change that:

- **The class has a property named like the tag field.** The property is
  normalized as usual and no tag is added: `CustomEvent` above writes its own
  `kind`.
- **The object's class is not in the map**, for example a proxy generated
  for a mapped class. The tag of its nearest parent in the map is used.

```php
final class LoginEventProxy extends LoginEvent {}

$proxy = new LoginEventProxy();
$proxy->id = '5';
$proxy->user = 'bob';

echo \json_encode($mapper->normalize($proxy, Event::class)), "\n";
echo \json_encode($mapper->normalize(new CustomEvent(), Event::class)), "\n";
```

```
{"kind":"login","user":"bob","id":"5"}
{"kind":"custom:payment","id":""}
```

<warning>
The tag is added only when the mapper knows that the value is of the base
type. <code>normalize($value)</code> without a type looks at the object's own
class, which carries no discriminator, and writes no tag. Pass the base type
to <code>normalize()</code> to get it:
<code-block lang="php">
echo \json_encode($mapper->normalize($proxy)), "\n";
// {"user":"bob","id":"5"}

echo \json_encode($mapper->normalize($proxy, Event::class)), "\n";
// {"kind":"login","user":"bob","id":"5"}
</code-block>
Inside a typed structure (a property typed <code>Shape</code>, a
<code>list&lt;Shape&gt;</code>) the type is known and the tag is written.
</warning>

## Configuration Files

The same map can be declared in a configuration file under the
`discriminator` key of the base class, with the same `field`, `map` and
`otherwise` keys. Class names in a file are fully qualified:

```yaml
App\Geometry\Shape:
  discriminator:
    field: type
    map:
      circle: App\Geometry\Circle
      square: App\Geometry\Square
    otherwise: App\Geometry\Circle
```

See [Metadata](mapper-metadata.md) for loading configuration files.
