# Property Accessor

Reads and writes properties of an object by name, regardless of their
visibility.

```php
use TypeLang\Mapper\PropertyAccessor\PropertyAccessorFactory;

$factory = new PropertyAccessorFactory();

$accessor = $factory->create($user);

if ($accessor->isWritable('email')) {
    $accessor->write('email', 'user@example.com');
}

echo $accessor->read('email'); // user@example.com
```
