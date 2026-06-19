# Platforms

A **platform** is a list of predefined types designed to transform data 
for a specific context. Depending on the chosen platform, the set of types, 
their behavior, and the resulting data may change.

To select a specific platform (other than the general standard one), you 
should create platform's instance and pass it to the `Mapper` constructor, 
so your code might look like this:

```php
$protobuf = new TypeLang\Mapper\Mapper(
    platform: new Example\Platform\Protobuf(),
);

$postgres = new TypeLang\Mapper\Mapper(
    platform: new Example\Platform\Postgres(),
);
```

This way two different instances will behave differently.

```php
final readonly class Message
{
    public function __construct(
        public int $id,
    ) {}
}

var_dump($protobuf->normalize(new Message(150)));
// string(3) "\x08\x96\x01"

var_dump($postgres->normalize(new Message(150)));
// array:1 [
//   "id" => 150
// ]
```

