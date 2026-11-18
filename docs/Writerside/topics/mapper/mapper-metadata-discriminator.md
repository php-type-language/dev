# Discriminator Map

<primary-label ref="mapper-component"/>
<secondary-label ref="beta"/>
<show-structure for="chapter" depth="2"/>

Polymorphism: the class of the data is selected by the value of a field.

Every option is shown in each [metadata source](mapper-metadata.md): choose
the tab of the source you use. When a source cannot express an option, its
tab says what is used instead.

## Declaring the Map

The map is declared on a base class or an interface.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\DiscriminatorMap;

#[DiscriminatorMap(field: 'type', map: [
    'circle' => Circle::class,
    'square' => Square::class,
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
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
There is no PHPDoc tag for this option.
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<note>
PHP has no such declaration: the data is mapped to the declared class itself.
</note>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\Shape::class => [
        'discriminator' => [
            'field' => 'type',
            'map' => [
                'circle' => App\Circle::class,
                'square' => App\Square::class,
            ],
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\Shape": {
        "discriminator": {
            "field": "type",
            "map": {
                "circle": "App\\Circle",
                "square": "App\\Square"
            }
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\Shape:
    discriminator:
        field: type
        map:
            circle: App\Circle
            square: App\Square
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\Shape:
    discriminator:
        field: type
        map:
            circle: App\Circle
            square: App\Square
```

</tab>
</tabs>

```php
$mapper->denormalize(['type' => 'circle', 'radius' => 2], Shape::class);
// Circle { radius: 2.0 }

$mapper->denormalize(['type' => 'oval'], Shape::class);
// throws InvalidValueException: $.type: Value must be of type
// 'circle'|'square', but "oval" given

$mapper->denormalize(['radius' => 2], Shape::class);
// throws InvalidValueException: $.type: Required field "type" is missing
```

## Fallback Type

The `otherwise` type is used when the tag is missing or unknown.

<tabs group="metadata">
<tab title="Attributes" group-key="attributes">

```php
use TypeLang\Mapper\Mapping\DiscriminatorMap;

#[DiscriminatorMap(field: 'type', map: [
    'circle' => Circle::class,
], otherwise: UnknownShape::class)]
abstract class Shape {}

final class Circle extends Shape
{
    public float $radius;
}

final class UnknownShape extends Shape {}
```

</tab>
<tab title="PHPDoc" group-key="phpdoc">

<warning>
There is no PHPDoc tag for this option.
</warning>

</tab>
<tab title="Reflection" group-key="reflection">

<note>
PHP has no such declaration: a missing or unknown tag is an error.
</note>

</tab>
<tab title="PHP Config" group-key="php-config">

```php
<?php

return [
    App\Shape::class => [
        'discriminator' => [
            'field' => 'type',
            'map' => [
                'circle' => App\Circle::class,
            ],
            'otherwise' => App\UnknownShape::class,
        ],
    ],
];
```

</tab>
<tab title="JSON" group-key="json">

```json
{
    "App\\Shape": {
        "discriminator": {
            "field": "type",
            "map": {
                "circle": "App\\Circle"
            },
            "otherwise": "App\\UnknownShape"
        }
    }
}
```

</tab>
<tab title="YAML" group-key="yaml">

```yaml
App\Shape:
    discriminator:
        field: type
        map:
            circle: App\Circle
        otherwise: App\UnknownShape
```

</tab>
<tab title="NEON" group-key="neon">

```neon
App\Shape:
    discriminator:
        field: type
        map:
            circle: App\Circle
        otherwise: App\UnknownShape
```

</tab>
</tabs>

```php
$mapper->denormalize(['type' => 'oval'], Shape::class); // UnknownShape {}
```

## Normalization

The tag of the class is written as the first field. A subclass that is not
in the map, such as a proxy, takes the tag of its nearest parent in the map.

```php
use TypeLang\Mapper\Mapping\DiscriminatorMap;

#[DiscriminatorMap(field: 'type', map: ['circle' => Circle::class])]
abstract class Shape {}

class Circle extends Shape
{
    public float $radius = 1.5;
}

final class CircleProxy extends Circle {}

$mapper->normalize(new Circle(), Shape::class);
// ['type' => 'circle', 'radius' => 1.5]

$mapper->normalize(new CircleProxy(), Shape::class);
// ['type' => 'circle', 'radius' => 1.5]
```

<warning>
Pass the base type to <code>normalize()</code>: without it the object is
normalized by its own class, without the tag.
</warning>

```php
$mapper->normalize(new Circle()); // ['radius' => 1.5]
```

A class with its own property named as the tag field writes that property,
the tag is not added.
