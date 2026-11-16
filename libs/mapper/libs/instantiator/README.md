# Instantiator

Creates or finds the object of a class the data is written to, the way is
chosen once by the declaration of the class and its metadata.

```php
use TypeLang\Mapper\Instantiator\InstantiatorFactory;

$instantiator = (new InstantiatorFactory())
    ->createInstantiator(new \ReflectionClass(User::class), $metadata);

$user = $instantiator->instantiate(['id' => 42]);
```
