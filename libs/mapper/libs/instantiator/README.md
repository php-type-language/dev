# Instantiator

Creates new objects of a class, the way of creating is chosen once by the
declaration of the class.

```php
use TypeLang\Mapper\Instantiator\InstantiatorFactory;

$instantiator = (new InstantiatorFactory())
    ->createInstantiator(new \ReflectionClass(User::class));

$user = $instantiator->instantiate();
```
