# Instantiator

Creates objects of a class without the constructor, through the constructor
or a static factory method.

```php
use TypeLang\Mapper\Instantiator\InstantiatorFactory;

$factory = new InstantiatorFactory();

$money = $factory->createFromConstructor(Money::class)
    ->instantiate(['amount' => 100]);
```
