# Custom Type

<show-structure for="chapter" depth="2"/>

To implement a custom type, you should create a class that implements 
the `TypeLang\Mapper\Type\TypeInterface` interface.

This interface requires two methods to be implemented:
- `cast()` - Converts external data into a value of a specific type
- `match()` - Checks external data for type compliance

Let's see what your custom type will look like.

```php
use TypeLang\Mapper\Context\Context;
use TypeLang\Mapper\Type\TypeInterface;

final readonly class MyType implements TypeInterface
{
    public function match(mixed $value, Context $context): bool
    {
        // TODO: Implement match() method.
    }

    public function cast(mixed $value, Context $context): mixed
    {
        // TODO: Implement cast() method.
    }
}
```

This is your "template" that will be present in all types.

## Type Match

To add behavior there, you first need to define the task. Let's create `int16`
as an example. Its purpose will be to accept a PHP `int` and return a PHP `int`
limited to 2 bytes (16 bits), for example, to implement a
[`smallint` in PostgreSQL](https://www.postgresql.org/docs/current/datatype-numeric.html).
We will guarantee that the output value will not exceed this size.

First, you need to add a type check:

```php
public function match(mixed $value, Context $context): bool
{
    return \is_int($value)
        && $value >= -32768
        && $value <= 32767;
}

// ...
```

<note xmlns="">
You don't need to implement type cast rules, as the external
<a href="type-coercers.md">type coercion</a> system guarantees that the incoming
value will be a PHP <code>int</code> (or will be converted to one).

If the casting rules differ from the built-in ones, you should implement 
<a href="custom-type-coercer.md">your own coercer</a> instead of writing 
code inside the type.
</note>

The `match()` method will be executed when calling the `Mapper::isNormalizable()` 
method, `Mapper::isDenormalizable()` an in union types, like: `int16|string`.

## Type Cast

After implementing the `match()` method, the second `cast()` 
method should be implemented. This method should return the "casted" value. 
In our case, `int16`:

```php
// ...

public function cast(mixed $value, Context $context): mixed
{
    if (!$this->match($value, $context)) {
        throw InvalidValueException::createFromContext($value);
    }

    return $value;
}
```

This is an extremely simple type that guarantees that for any input data, 
the output will be a value within `int<-32768, 32767>`.

Throwing an `TypeLang\Mapper\Exception\Runtime\InvalidValueException` exception 
in the `cast()` method is a simple and convenient way to report a problem 
with a value. You can use your own exceptions, such as the built-in 
`\InvalidArgumentException`, but this is **not recommended**.

So the complete code will look like this.

```php
use TypeLang\Mapper\Context\Context;
use TypeLang\Mapper\Type\TypeInterface;
use TypeLang\Mapper\Exception\Runtime\InvalidValueException;

/**
 * @template-implements TypeInterface<int<-32768, 32767>>
 */
final readonly class Int16Type implements TypeInterface
{
    public function match(mixed $value, Context $context): bool
    {
        return \is_int($value)
            && $value >= -32768
            && $value <= 32767;
    }

    public function cast(mixed $value, Context $context): mixed
    {
        if (!$this->match($value, $context)) {
            throw InvalidValueException::createFromContext($value);
        }

        return $value;
    }
}
```

## Context

You may have noticed that in addition to the value itself, a `Context` object 
is passed to each method. This `Context` object is completely **immutable** and
contains runtime data: execution path; original value; configuration settings; 
more.


### Context Value

You can retrieve the original value using the `$context->value` field from the 
`Context` object. This value may differ from the value passed in; for example, 
[type coercion](type-coercers.md) rules are not applied to it, so it can be 
used in exceptions to report information about the original data passed by 
the user.

```php
public function cast(mixed $value, Context $context): mixed
{
    var_dump('Coerced Value: ', $value);
    var_dump('Original Value: ', $context->value);
}
```


### Direction

Mapping direction is also runtime information. In most cases, different 
types will be used for different directions (if the behavior during 
<tooltip term="normalize">normalization</tooltip> and 
<tooltip term="denormalize">denormalization</tooltip> differs), but 
sometimes you may need this information.

Information about the direction is located in the `direction` field of 
the `Context` object.

```php
use TypeLang\Mapper\Context\Direction;

// ...

public function cast(mixed $value, Context $context): mixed
{
    if ($context->direction === Direction::Normalize) {
        return 'cast from php type';
    }
    
    return 'cast to php type';
}
```


### Configuration

As you might guess, the `config` property contains 
[a `Configuration` object](mapper-configuration.md). However, it's important to note that the 
configuration object inside `Mapper` and the configuration object inside 
`Context` are **different**. For example, rules such as 
[`Configuration::$objectAsArray`](mapper-configuration.md#object-as-array) and
[`Configuration::$strictTypes`](mapper-configuration.md#strict-types) can be 
modified for a specific type. Thus, the configuration object will contain the 
**new values** specifically for that context.

For ease of use, methods such as `isObjectAsArray()` and `isStrictTypesEnabled()`
can be called directly on the `Context` object.

<tip>
Calls <code>$context->config->isStrictTypesEnabled()</code> and 
<code>$context->isStrictTypesEnabled()</code> are completely identical
</tip>

```php
public function match(mixed $value, Context $context): bool
{
    if ($context->isStrictTypesEnabled()) {
        // strict match required
    }

    // non-strict match required
}
```


### Child Context

To process any composite types, such as PHP `array`s or `object`s, a child 
context must be created. As mentioned above, the `Context` object itself is 
immutable, so to create a child context, a new object must be created using the
`enter()` method, specifying information about the element being "entered".

For example, we have a task to create a type that should return an 
`list<non-empty-string>` from an input `iterable<array-key, mixed>` type.

An implementation will be look like this:

```php
public function cast(mixed $value, Context $context): mixed
{
    // We get the type instance corresponding to "non-empty-string"
    $stringType = $context->getTypeByDefinition('non-empty-string');

    $result = [];

    foreach ($value as $index => $item) {
        // Creating child context
        $subContext = $context->enterIntoArrayIndex($item, $index);

        // Apply a "non-empty-string" cast to the "$item"
        $result[] = $stringType->cast($item, $subContext);
    }

    return $result;
}
```

<note>
You may notice that retrieving a type by its name 
(<code>->getTypeByDefinition('non-empty-string')</code>) occurs at runtime for 
every call to the <code>cast()</code> method within the type. 

This isn't a very optimal approach, and it's recommended to instead pass 
such things to the <code>__construct()</code> once, keeping the 
<code>cast()</code> and <code>match()</code> methods as "lightweight" as 
possible, containing only runtime expressions.
</note>

When creating a child context, you can also change the configuration rules by 
specifying it as the third argument.

```php
public function cast(mixed $value, Context $context): mixed
{
    // ...
    
    foreach ($value as $index => $item) {
        // Creating child context
        $subContext = $context->enterIntoArrayIndex(
            value: $item,
            index: $index,
            config: $context->withStrictTypes(false),
        );

        // ...
    }
    
    // ...
}
```

<note>
Please note that method <code>$context-&gt;withStrictTypes()</code> is used
instead of <code>$context-&gt;config-&gt;withStrictTypes()</code>.
<list>
<li>
The <code>$context-&gt;withStrictTypes()</code> takes 
the <b>original</b> configuration specified in the <code>Mapper</code>'s 
constructor rules and adds information about the <code>strictTypes</code> 
settings to it.
</li>
<li>
The <code>$context-&gt;config-&gt;withStrictTypes()</code> will
supplement the already <b>modified</b> configuration of the current context.
</li>
</list>
</note>


### Parent Context

In addition to the ability to create children, a context can also have a parent 
context that contains all the information about the `Context` of the parent type.

Please note that a parent is only contained by the context that has it, i.e., 
only the child. Therefore, be sure to perform the appropriate check.

```php
use TypeLang\Mapper\Context\ChildContext;

// ...

public function cast(mixed $value, Context $context): mixed
{
    var_dump('Current context is:', $context);

    var_dump('Has parent context:', $context instanceof ChildContext);
    var_dump('Parent context is:', $context?->parent);

    return;
}
```


### Context Path

During execution, the `Context` collects the path the mapper traverses. 
This path can then be displayed in error messages or for other information.

To get the current path, call the `getPath()` method. This method will 
recursively traverse the entire history and return 
`TypeLang\Mapper\Context\Path\PathInterface` path object.

For example, we can print all the elements of the path in the type:

```php
public function cast(mixed $value, Context $context): mixed
{
    foreach ($context->getPath() as $entry) {
        echo sprintf("> %s\n", $entry);
    }

    return;
}
```

### Interact With Types

The context contains a set of methods for obtaining types available in the 
current "mapping process".

The most necessary and popular ones will be the following:

- `$context->getTypeByValue($value)` - Returns an object of `TypeInterface` by 
  passed PHP value. Where value is an absolutely arbitrary value 
  whose type will be [automatically inferred](type-extractors.md).
- `$context->getTypeByDefinition($type)` - Returns an object of `TypeInterface` 
  by passed type definition. Where definition is a PHP string with a description 
  of the type, for example `$type = "int<0, max>"`.

In addition, the context contains several helper methods that are worth knowing
about, but you are unlikely to need them:

- `$context->getStatementByValue($value)` - Returns an Abstract Syntax Tree 
  (AST) of the [inferred type](type-extractors.md) of the value.
- `$context->getStatementByDefinition($type)` - Returns an AST by the type 
  definition string.
- `$context->getTypeByStatement($ast)` - Returns an object of `TypeInterface` by 
  the type's AST.