# Property Accessor

Reads and writes one property of any object of its class, regardless of its
visibility, the way of access is chosen once by the declaration of the
property.

```php
use TypeLang\Mapper\PropertyAccessor\PropertyAccessorFactory;

$property = new \ReflectionProperty(User::class, 'email');
$factory = new PropertyAccessorFactory();

$factory->createWriter($property)->write($user, 'user@example.com');

echo $factory->createReader($property)->read($user); // user@example.com
```
