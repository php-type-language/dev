# Custom Platform

You can create your own platform, which will contain your own set of types and 
rules for processing them.

```php
use TypeLang\Mapper\Mapper;

$mapper = new Mapper(new CustomPlatform());
```

For ease of use, you can extend from the abstract class 
`TypeLang\Mapper\Platform\Platform`, the implementation of which is 
suitable for most cases.

To implement a platform, you should implement a single method `getName()`, 
which should return the custom name of the platform.

```php
use TypeLang\Mapper\Platform\Platform;

class CustomPlatform extends Platform
{
    public function getName(): string
    {
        return 'custom';
    }
}
```

You can already use it by simply specifying a `new CustomPlatform()` instance 
in the mapper, as in the example above.

## Adding Types

Each platform contains a list of supported types. If you don't specify these
explicitly, the platform will be **empty**, meaning it won't support any types.
To extend these types, override the `getTypes()` method:

```php
use TypeLang\Mapper\Platform\Platform;
use TypeLang\Mapper\Context\Direction;
use TypeLang\Mapper\Type\Builder\TypeBuilderInterface;

class CustomPlatform extends Platform
{
    // public function getName(): string ...

    /**
     * @return iterable<array-key, TypeBuilderInterface>
     */
    public function getTypes(Direction $direction): iterable
    {
        // List of types passed to the CustomPlatform constructor
        yield from parent::getTypes($direction);

        // The default list of types for the "custom" platform
        yield new CustomTypeBuilder();
        yield new AnotherCustomTypeBuilder();
    }
}
```

<warning xmlns="">
Note that the <code>getTypes()</code> method returns a list of type builders 
(implementations of the <code>TypeLang\Mapper\Type\Builder\TypeBuilderInterface</code> 
interface), not the types (<code>TypeLang\Mapper\Type\TypeInterface</code>) themselves.
</warning>

As you may have noticed, your `getTypes()` method contains a call to 
`parent::getTypes()`. This will return the list of types you pass to the
`new CustomPlatform()` constructor.

```php
$platform = new CustomPlatform(types: [
    new OverrideCustomTypeBuilder(),
    new AnotherOverrideCustomTypeBuilder(),
]);
```

The ability to override/add types is provided by the abstract
`TypeLang\Mapper\Platform\Platform` class, from which the platform extends.

Yes, you can remove this ability. To do so, simply remove the call to
`parent::getTypes()` from the method or implement 
`TypeLang\Mapper\Platform\PlatformInterface` instead of abstract 
`TypeLang\Mapper\Platform\Platform` class.

<tip>
You can read more about builders in the 
<a href="type-builders.md">"type builders" page</a>.

More information about the types themselves can be found in 
<a href="types.md">"types" page</a>.
</tip>

## Adding Type Coercions

By default, your platform doesn't implement any type casts (type coercions). 
Therefore, the ["strict types" configuration option](mapper-configuration.md#strict-types) 
doesn't actually work.

If you want to support any type casts, you should explicitly specify which
types the cast applies  to by overriding the `getTypeCoercers()` method:

```php

```php
use TypeLang\Mapper\Platform\Platform;
use TypeLang\Mapper\Context\Direction;
use TypeLang\Mapper\Type\TypeInterface;
use TypeLang\Mapper\Type\Coercer\TypeCoercerInterface;

class CustomPlatform extends Platform
{
    // public function getName(): string ...
    // public function getTypes(Direction $direction): iterable ...

    /**
     * @return iterable<class-string<TypeInterface>, TypeCoercerInterface>
     */
    public function getTypeCoercers(Direction $direction): iterable
    {
        yield ExampleType::class => new ExampleTypeCoercer();
    }
}
```

<tip>
You can read more about coercers in the 
<a href="type-coercers.md">"type coercers" page</a>.
</tip>