# Metadata Configuration

<show-structure for="chapter" depth="3"/>

Metadata allows you to configure rules for processing objects and supplementing
information. There are several metadata formats, but their capabilities are 
almost identical. You can choose any format that suits you best, such 
[as PHP Attributes](meta-reader-attribute.md), 
configuration [file in YAML format](meta-reader-yaml-config.md), 
or any other format.

<warning>
If you specify the configuration using 
<a href="meta-reader-array.md">PHP Array</a>,
<a href="meta-reader-php-config.md">PHP Config</a>,
<a href="meta-reader-yaml-config.md">YAML Config</a>,
<a href="meta-reader-neon-config.md">NEON Config</a>, 
<a href="meta-reader-json-config.md">JSON Config</a> or other similar 
"configuration-based" methods, then it is worth knowing that by default, 
the configuration  is <b>not validated</b>.
</warning>

<note>
To enable validation of "configuration-based" methods, you should add
<code>justinrainbow/json-schema</code> package.

<code-block lang="shell">
composer require justinrainbow/json-schema --dev
</code-block>

The schema (as well as <code>justinrainbow/json-schema</code>package) is
<b>not required</b> if you use <a href="meta-reader-phpdoc.md">PHPDoc</a>,
<a href="meta-reader-attribute.md">Attributes</a>, and other similar
configuration methods.
</note>

<tip>
If you need the <a href="https://json-schema.org/">JSON Schema</a> for this
config, you can find it in the repository
<a href="https://github.com/php-type-language/mapper/blob/master/resources/config.schema.json">
in the <code>resources</code> directory</a>.
</tip>

## Class Metadata

There are several rules that may apply to a specific class. Detailed 
information on these rules is provided below.

### Normalize As Array

This option is responsible for converting an object to an associative
array during serialization.

- In case of the `true` value specified, the object will be converted to
  an associative array during normalization
- In case of the `false` value specified, the object will be converted to
  an object (instance of `StdClass`) during normalization
- If you **omit** this option, the default value specified
  [in the configuration](mapper-configuration.md#object-as-array) will be used


<tabs>
<tab title="Attributes">
<code-block lang="PHP">
use TypeLang\Mapper\Mapping\NormalizeAsArray;

#[NormalizeAsArray]
final class WithArrayNormalization
{
    // ...
}

#[NormalizeAsArray(enabled: false)]
final class WithoutArrayNormalization
{
    // ...
}
</code-block>
</tab>
<tab title="PHPDoc">
<warning>
You cannot control this option using PHPDoc
</warning>
</tab>
<tab title="Reflection">
<warning>
You cannot specify this option at the PHP class level
</warning>
</tab>
<tab title="PHP Config">
<code-block lang="PHP">
// App.Example.WithArrayNormalization.php
return [
    'normalize_as_array' => true,
    // ...
];
</code-block>
<code-block lang="PHP">
// App.Example.WithoutArrayNormalization.php
return [
    'normalize_as_array' => false,
    // ...
];
</code-block>
</tab>
<tab title="JSON">
<code-block lang="json5">
// App.Example.WithArrayNormalization.json
{
    "normalize_as_array": true,
    // ...
}
</code-block>
<code-block lang="json5">
// App.Example.WithoutArrayNormalization.json
{
    "normalize_as_array": false,
    // ...
}
</code-block>
</tab>
<tab title="YAML">
<code-block lang="yaml">
# App.Example.WithArrayNormalization.yaml
normalize_as_array: true
# ...
</code-block>
<code-block lang="yaml">
# App.Example.WithoutArrayNormalization.yaml
normalize_as_array: false
# ...
</code-block>
</tab>
<tab title="NEON">
<code-block lang="neon">
# App.Example.WithArrayNormalization.neon
normalize_as_array: true
# ...
</code-block>
<code-block lang="neon">
# App.Example.WithoutArrayNormalization.neon
normalize_as_array: false
# ...
</code-block>
</tab>
</tabs>

### Discriminator Map

A map discriminator is a set of rules for inferring a type based 
on the value of a field.

For example, we have the following class hierarchy.

```php
abstract class UserInfo
{
    public function __construct(
        public string $name,
    ) {}
}

final class AdminUserInfo extends UserInfo {}
final class GuestUserInfo extends UserInfo {}
```

In this case, when trying to denormalize the class `UserInfo`, an error
will occur since there will be an attempt to create an `abstract` class.
```php
$result = (new TypeLang\Mapper\Mapper())
    ->denormalize([
        'name' => 'Kirill',
    ], UserInfo::class);

// TypeLang\Mapper\Exception\Runtime\NonInstantiatableException: 
// Unable to instantiate "UserInfo" of UserInfo{name: string}
```

А similar error will occur when instantiating an `interface`.

To solve this problem, there is a discriminator map, with the help of which 
you can specify which class the data belongs to.

For example, we want that:
- When passing a `'type' => 'admin'`, an object `AdminUserInfo` is created
- When passing a `'type' => 'guest'`, an object `GuestUserInfo` is created

<compare first-title="AdminUserInfo" second-title="GuestUserInfo">
<code-block lang="php">
// Expects object(AdminUserInfo)
$result = $mapper->denormalize([
    'name' => 'Kirill',
    'type' => 'admin',
], UserInfo::class);
</code-block>
<code-block lang="php">
// Expects object(GuestUserInfo)
$result = $mapper->denormalize([
    'name' => 'Kirill',
    'type' => 'guest',
], UserInfo::class);
</code-block>
</compare>

The metadata configuration for this rule will look like this:

<tabs>
<tab title="Attributes">
<code-block lang="PHP">
use TypeLang\Mapper\Mapping\DiscriminatorMap;

#[DiscriminatorMap('type', [
    'admin' => AdminUserInfo::class,
    'guest' => GuestUserInfo::class,
])]
abstract class UserInfo
{
    // ...
}
</code-block>
</tab>
<tab title="PHPDoc">
<warning>
You cannot control this option using PHPDoc
</warning>
</tab>
<tab title="Reflection">
<warning>
You cannot specify this option at the PHP class level
</warning>
</tab>
<tab title="PHP Config">
<code-block lang="PHP">
// UserInfo.php
return [
    'discriminator' => [
        'field' => 'type',
        'map' => [
            'admin' => AdminUserInfo::class,
            'guest' => GuestUserInfo::class,
        ],
    ],
];
</code-block>
</tab>
<tab title="JSON">
<code-block lang="json5">
// UserInfo.json
{
    "discriminator": {
        "field": "type",
        "map": {
            "admin": "AdminUserInfo",
            "guest": "GuestUserInfo"
        }
    }
}
</code-block>
</tab>
<tab title="YAML">
<code-block lang="yaml">
# UserInfo.yaml
discriminator:
  field: type
  map:
    admin: AdminUserInfo
    guest: GuestUserInfo
</code-block>
</tab>
<tab title="NEON">
<code-block lang="neon">
# UserInfo.neon
discriminator:
  field: type
  map:
    admin: AdminUserInfo
    guest: GuestUserInfo
</code-block>
</tab>
</tabs>


#### Default Type

When specifying such a configuration, another error may occur if the `"type"` 
field is not passed or an incorrect value is passed.

```php
$result = (new \TypeLang\Mapper\Mapper())
    ->denormalize([
        'name' => 'Kirill',
    ], UserInfo::class);

// Object {"name": "Kirill"} requires missing field 
// "type" of type "admin"|"guest"
```

```php
$result = (new \TypeLang\Mapper\Mapper())
    ->denormalize([
        'name' => 'Kirill',
        'type' => 'unknown',
    ], UserInfo::class);

// Passed value in "type" of {"name": "Kirill", "type": "unknown"} 
// must be of type "admin"|"guest", but "unknown" given
```

To solve such problems, you can specify a default value (default type) that 
will be used in case of an incorrect or missing field in the discriminator map.

Let's define that if the map discriminator fails, then we will create 
a `GuestUserInfo` instance.

<tabs>
<tab title="Attributes">
<code-block lang="PHP">
use TypeLang\Mapper\Mapping\DiscriminatorMap;

#[DiscriminatorMap('type', [
    'admin' => AdminUserInfo::class,
    'guest' => GuestUserInfo::class,
], otherwise: GuestUserInfo::class)]
abstract class UserInfo
{
    // ...
}
</code-block>
</tab>
<tab title="PHPDoc">
<warning>
You cannot control this option using PHPDoc
</warning>
</tab>
<tab title="Reflection">
<warning>
You cannot specify this option at the PHP class level
</warning>
</tab>
<tab title="PHP Config">
<code-block lang="PHP">
// UserInfo.php
return [
    'discriminator' => [
        'field' => 'type',
        'map' => [
            'admin' => AdminUserInfo::class,
            'guest' => GuestUserInfo::class,
        ],
        'otherwise' => GuestUserInfo::class,
    ],
];
</code-block>
</tab>
<tab title="JSON">
<code-block lang="json5">
// UserInfo.json
{
    "discriminator": {
        "field": "type",
        "map": {
            "admin": "AdminUserInfo",
            "guest": "GuestUserInfo"
        },
        "otherwise": "GuestUserInfo"
    }
}
</code-block>
</tab>
<tab title="YAML">
<code-block lang="yaml">
# UserInfo.yaml
discriminator:
  field: type
  map:
    admin: AdminUserInfo
    guest: GuestUserInfo
  otherwise: GuestUserInfo
</code-block>
</tab>
<tab title="NEON">
<code-block lang="neon">
# UserInfo.neon
discriminator:
  field: type
  map:
    admin: AdminUserInfo
    guest: GuestUserInfo
  otherwise: GuestUserInfo
</code-block>
</tab>
</tabs>

<tip>
You don't have to specify specific classes in your mapping rules, 
you can specify <b>any other types</b>.

<code-block lang="php">
#[DiscriminatorMap('type', [
    // If "type" contain "first" then create an object
    'first' => 'object',

    // If "type" contain "second" then create an array
    'second' => 'array&lt;array-key, non-empty-string>',

    // When specifying another class or interface,
    // and it has discriminator map config, 
    // they will also be applied
    'other' => OtherAbstractClass::class,
])]
abstract class UserInfo { ... }
</code-block>
</tip>

### Properties

For each class, you can specify a list of properties and their types that 
will be involved in <tooltip term="normalize">normalization</tooltip> and
<tooltip term="denormalize">denormalization</tooltip> process.

For example, we have the following class:

```php
final class UserInfo
{
    public function __construct(
        public mixed $name,
        public mixed $other {
            get => $this->other;
            set(mixed $other) => (string) $other;
        },
    ) {}
}
```

To specify properties and their types, you can use the following settings

<tabs>
<tab title="Attributes">
<code-block lang="PHP">
use TypeLang\Mapper\Mapping\MapType;

final class UserInfo
{
    public function __construct(
        #[MapType('string')]
        public mixed $name,
        #[MapType('string')]
        public mixed $other {
            get => $this->other;
            #[MapType('string|Stringable')]
            set(mixed $other) => (string) $other;
        },
    ) {}
}
</code-block>

As you can see, the `#[MapType]` attribute can be set on both properties 
and property hooks. Depending on where you specify the attribute, the 
corresponding rule will be applied.

<code-block lang="PHP">
#[MapType(...)]                 // Read + Write type
public mixed $other {
    #[MapType(...)]             // Read type
    get => $this->other;
    #[MapType(...)]             // Write type
    set(mixed $other) => ...;
},
</code-block>
</tab>
<tab title="PHPDoc">
<code-block lang="PHP">
final class UserInfo
{
    public function __construct(
        /**
         * @var string
         */
        public mixed $name,
        /**
         * @var string
         */
        public mixed $other {
            get => $this->other;
            /**
             * @param string|Stringable $other
             */
            set(mixed $other) => (string) $other;
        },
    ) {}
}
</code-block>

For promoted properties, you can also use parameter (`@param`) PHPDoc 
annotations.
<code-block lang="PHP">
final class UserInfo
{
    /**
     * @param string $name
     * @param string $other
     */
    public function __construct(
        public mixed $name,
        public mixed $other {
            get => $this->other;
            /**
             * @param string|Stringable $other
             */
            set(mixed $other) => (string) $other;
        },
    ) {}
}
</code-block>

Besides this, as you can see, an annotations can be set on arguments, 
properties and property hooks. Depending on where you specify the annotation, 
the corresponding rule will be applied.

<code-block lang="PHP">
final class UserInfo
{
    /**
     * This annotation is read first and assumes read + write 
     * type "PARAM_TYPE" to promoted property $other
     * 
     * @param PARAM_TYPE $other
     */
    public function __construct(
        /**
         * Next, the annotation of the property is read and 
         * overrides the read + write types by "PROPERTY_TYPE"
         *
         * This annotation has a higher priority than "param"
         *
         * @var PROPERTY_TYPE
         */
        public mixed $other {
            /**
             * If the "return" annotation is specified on the
             * hook's "getter", the read type is directly the
             * type "GETTER_TYPE"
             *
             * The annotation replaces the READ type exclusively
             *
             * @return GETTER_TYPE
             */
            get => $this->other;
            /**
             * If the "param" annotation is specified on the
             * hook's "setter", the write type is directly the
             * type "SETTER_TYPE"
             *
             * The annotation replaces the WRITE type exclusively
             * 
             * @param SETTER_TYPE $other
             */
            set(mixed $other) => (string) $other;
        },
    ) {}
}
</code-block>
</tab>
<tab title="Reflection">
To use reflection, simply specify the types. Please note that only 
<b>public</b> properties are read.

<code-block lang="PHP">
final class UserInfo
{
    public function __construct(
        public string $name,
        public string $other {
            get => $this->other;
            set(string|Stringable $other) => (string) $other;
        },
        private string $hiddenProperty,
    ) {}
}
</code-block>
</tab>
<tab title="PHP Config">
<code-block lang="PHP">
// UserInfo.php
return [
    'properties' => [
        'name' => 'string',
        'other' => [
            'type' => 'string',
            'write' => 'string|Stringable',
        ],
    ],
];
</code-block>
</tab>
<tab title="JSON">
<code-block lang="json5">
// UserInfo.json
{
    "properties": {
        "name": "string",
        "other": {
            "type": "string",
            "write": "string|Stringable"
        }
    }
}
</code-block>
</tab>
<tab title="YAML">
<code-block lang="yaml">
# UserInfo.yaml
properties:
  name: string
  other:
    type: string
    write: string|Stringable
</code-block>
</tab>
<tab title="NEON">
<code-block lang="neon">
# UserInfo.neon
properties:
  name: string
  other:
    type: string
    write: string|Stringable
</code-block>
</tab>
</tabs>

## Property Metadata

As you may have noticed above, a class can contain a collection of properties.
However, for each property, you can also specify a list of additional rules.
One such rule is the type, the configuration information for which is
available above in ["properties" section](meta-configuration.md#properties).

Let's look at other configuration rules.

### Strict Types

For each property, you can also separately specify "type strictness" rules, 
more details about which can be found either [in the configuration](mapper-configuration.md) 
or in the section with [type coercers](type-coercers.md).

The "strict types" option can take one of two possible values, or may 
not be specified:
- In case of the `true` value specified, then "strict types" will be 
  applied for the property
- In case of the `false` value specified, then "strict types" will be 
  disabled for the property
- If you **omit** this option, the default value specified
  [in the configuration](mapper-configuration.md#strict-types) will be used


Let's take the following class and apply different strictness rules 
to each property:

```php
final class UserInfo
{
    public function __construct(
        // Type should be strict
        public string $strict,
        // Type should be non-strict
        public string $notStrict,
    ) {}
}
```

<tabs>
<tab title="Attributes">
<code-block lang="PHP">
use TypeLang\Mapper\Mapping\MapType;

final class UserInfo
{
    public function __construct(
        #[MapType('string', strict: true)]
        public string $strict,
        #[MapType('string', strict: false)]
        public string $notStrict,
    ) {}
}
</code-block>
</tab>
<tab title="PHPDoc">
<warning>
You cannot control this option using PHPDoc
</warning>
</tab>
<tab title="Reflection">
<warning>
You cannot specify this option at the PHP class level
</warning>
</tab>
<tab title="PHP Config">
<code-block lang="PHP">
// UserInfo.php
return [
    'properties' => [
        'strict' => [
            'type' => 'string',
            'strict' => true,
        ],
        'notStrict' => [
            'type' => 'string',
            'strict' => false,
        ],
    ],
];
</code-block>
</tab>
<tab title="JSON">
<code-block lang="json5">
// UserInfo.json
{
    "properties": {
        "strict": {
            "type": "string",
            "strict": true
        },
        "notStrict": {
            "type": "string",
            "strict": false
        }
    }
}
</code-block>
</tab>
<tab title="YAML">
<code-block lang="yaml">
# UserInfo.yaml
properties:
  strict:
    type: string
    strict: true
  notStrict:
    type: string
    strict: false
</code-block>
</tab>
<tab title="NEON">
<code-block lang="neon">
# UserInfo.neon
properties:
  strict:
    type: string
    strict: true
  notStrict:
    type: string
    strict: false
</code-block>
</tab>
</tabs>


### Rename (Alias)

You can specify the name of the property to be used during normalization. 
This way, when normalizing an object with property, it will be "published" 
under a different name.

<tabs>
<tab title="Attributes">
<code-block lang="PHP">
use TypeLang\Mapper\Mapping\MapName;

final class UserInfo
{
    public function __construct(
        #[MapName('public_name')]
        public string $localName,
    ) {}
}
</code-block>
</tab>
<tab title="PHPDoc">
<warning>
You cannot control this option using PHPDoc
</warning>
</tab>
<tab title="Reflection">
<warning>
You cannot specify this option at the PHP class level
</warning>
</tab>
<tab title="PHP Config">
<code-block lang="PHP">
// UserInfo.php
return [
    'properties' => [
        'localName' => [
            'name' => 'public_name',
        ],
    ],
];
</code-block>
</tab>
<tab title="JSON">
<code-block lang="json5">
// UserInfo.json
{
    "properties": {
        "localName": {
            "name": "public_name"
        }
    }
}
</code-block>
</tab>
<tab title="YAML">
<code-block lang="yaml">
# UserInfo.yaml
properties:
  localName:
    name: public_name
</code-block>
</tab>
<tab title="NEON">
<code-block lang="neon">
# UserInfo.neon
properties:
  localName:
    name: public_name
</code-block>
</tab>
</tabs>


### Custom Type Error Message

In some cases, you may need to override the error message when type errors 
occur. You can do this explicitly for a specific class property.

Please note that you also have access to template variables that substitute 
specific runtime values:
- `{{field}}` - Property name (or [public alias](meta-configuration.md#rename-alias))
- `{{expected}}` - Expected type
- `{{value}}` - Actual value
- `{{path}}` - The path to the property where the error occurred
- `{{file}}` - PHP file in which the error occurred
- `{{line}}` - The line in the PHP code where the error occurred
- `{{code}}` - An error code

Let's specify an error message for property `string $name`, like:

```text
The {{field}} is invalid
```

Thus, if you pass an empty array (`[]`) to `"name"` property, the following 
error should occur:

```text
The "name" is invalid
```

<tabs>
<tab title="Attributes">
<code-block lang="PHP">
use TypeLang\Mapper\Mapping\OnTypeError;

final class UserInfo
{
    public function __construct(
        #[OnTypeError('The {{field}} is invalid')]
        public string $name,
    ) {}
}
</code-block>
</tab>
<tab title="PHPDoc">
<warning>
You cannot control this option using PHPDoc
</warning>
</tab>
<tab title="Reflection">
<warning>
You cannot specify this option at the PHP class level
</warning>
</tab>
<tab title="PHP Config">
<code-block lang="PHP">
// UserInfo.php
return [
    'properties' => [
        'name' => [
            'type_error_message' => 'The {{field}} is invalid',
        ],
    ],
];
</code-block>
</tab>
<tab title="JSON">
<code-block lang="json5">
// UserInfo.json
{
    "properties": {
        "name": {
            "type_error_message": "The {{field}} is invalid"
        }
    }
}
</code-block>
</tab>
<tab title="YAML">
<code-block lang="yaml">
# UserInfo.yaml
properties:
  name:
    type_error_message: "The {{field}} is invalid"
</code-block>
</tab>
<tab title="NEON">
<code-block lang="neon">
# UserInfo.neon
properties:
  name:
    type_error_message: "The {{field}} is invalid"
</code-block>
</tab>
</tabs>

### Custom Undefined Error Message

If a required field was not passed, a different error may occur. 
You can also customize it using the corresponding configuration rules.

<tabs>
<tab title="Attributes">
<code-block lang="PHP">
use TypeLang\Mapper\Mapping\OnUndefinedError;

final class UserInfo
{
    public function __construct(
        #[OnUndefinedError('The {{field}} is required')]
        public string $name,
    ) {}
}
</code-block>
</tab>
<tab title="PHPDoc">
<warning>
You cannot control this option using PHPDoc
</warning>
</tab>
<tab title="Reflection">
<warning>
You cannot specify this option at the PHP class level
</warning>
</tab>
<tab title="PHP Config">
<code-block lang="PHP">
// UserInfo.php
return [
    'properties' => [
        'name' => [
            'undefined_error_message' => 'The {{field}} is required',
        ],
    ],
];
</code-block>
</tab>
<tab title="JSON">
<code-block lang="json5">
// UserInfo.json
{
    "properties": {
        "name": {
            "undefined_error_message": "The {{field}} is required"
        }
    }
}
</code-block>
</tab>
<tab title="YAML">
<code-block lang="yaml">
# UserInfo.yaml
properties:
  name:
    undefined_error_message: "The {{field}} is required"
</code-block>
</tab>
<tab title="NEON">
<code-block lang="neon">
# UserInfo.neon
properties:
  name:
    undefined_error_message: "The {{field}} is required"
</code-block>
</tab>
</tabs>


### Skip

During normalization, there may be cases where a property needs to be 
excluded based on certain criteria. For this, you can use 
"skip" metadata configuration rules.

#### When Empty

If you want to exclude empty properties (for example, empty arrays),
you can use the "when empty" rules

<tabs>
<tab title="Attributes">
<code-block lang="PHP">
use TypeLang\Mapper\Mapping\SkipWhenEmpty;

final class UserInfo
{
    public function __construct(
        #[SkipWhenEmpty]
        public iterable $friends = [],
    ) {}
}
</code-block>
</tab>
<tab title="PHPDoc">
<warning>
You cannot control this option using PHPDoc
</warning>
</tab>
<tab title="Reflection">
<warning>
You cannot specify this option at the PHP class level
</warning>
</tab>
<tab title="PHP Config">
<code-block lang="PHP">
// UserInfo.php
return [
    'properties' => [
        'friends' => [
            'skip' => ['empty'],
        ],
    ],
];
</code-block>
</tab>
<tab title="JSON">
<code-block lang="json5">
// UserInfo.json
{
    "properties": {
        "friends": {
            "skip": ["empty"]
        }
    }
}
</code-block>
</tab>
<tab title="YAML">
<code-block lang="yaml">
# UserInfo.yaml
properties:
  friends:
    skip:
      - "empty"
</code-block>
</tab>
<tab title="NEON">
<code-block lang="neon">
# UserInfo.neon
properties:
  friends:
    skip:
      - "empty"
</code-block>
</tab>
</tabs>


#### When Null

If you want to exclude properties that are strictly `null`, you 
should use the "when null" setting.

<tabs>
<tab title="Attributes">
<code-block lang="PHP">
use TypeLang\Mapper\Mapping\SkipWhenNull;

final class UserInfo
{
    public function __construct(
        #[SkipWhenNull]
        public ?string $name = null,
    ) {}
}
</code-block>
</tab>
<tab title="PHPDoc">
<warning>
You cannot control this option using PHPDoc
</warning>
</tab>
<tab title="Reflection">
<warning>
You cannot specify this option at the PHP class level
</warning>
</tab>
<tab title="PHP Config">
<code-block lang="PHP">
// UserInfo.php
return [
    'properties' => [
        'friends' => [
            'skip' => ['null'],
        ],
    ],
];
</code-block>
</tab>
<tab title="JSON">
<code-block lang="json5">
// UserInfo.json
{
    "properties": {
        "friends": {
            "skip": ["null"]
        }
    }
}
</code-block>
</tab>
<tab title="YAML">
<code-block lang="yaml">
# UserInfo.yaml
properties:
  friends:
    skip:
      - "null"
</code-block>
</tab>
<tab title="NEON">
<code-block lang="neon">
# UserInfo.neon
properties:
  friends:
    skip:
      - "null"
</code-block>
</tab>
</tabs>


#### Expression

In some cases, you may need to use more complex pass criteria. 
For these conditions, use the `symfony/expression-language` 
package.

<tip>
You can read more about expressions in the 
<a href="meta-expressions.md">&quot;expressions&quot; documentation</a>.

However, you need to know that to support them, you need 
to install additional package.

<code-block lang="shell">
composer require symfony/expression-language
</code-block>
</tip>


<tabs>
<tab title="Attributes">
<code-block lang="PHP">
use TypeLang\Mapper\Mapping\SkipWhen;

final class UserInfoResponse
{
    public function __construct(
        public ?string $firstName,
        public ?string $lastName,
        #[SkipWhen('this.firstName == null or this.lastName == null')]
        public string $fullName {
            get => $this->firstName . ' ' . $this->lastName;
        },
    ) {}
}
</code-block>
</tab>
<tab title="PHPDoc">
<warning>
You cannot control this option using PHPDoc
</warning>
</tab>
<tab title="Reflection">
<warning>
You cannot specify this option at the PHP class level
</warning>
</tab>
<tab title="PHP Config">
<code-block lang="PHP">
// UserInfo.php
return [
    'properties' => [
        'fullName' => [
            'skip' => [
                'this.firstName == null or this.lastName == null'
            ],
        ],
    ],
];
</code-block>
</tab>
<tab title="JSON">
<code-block lang="json5">
// UserInfo.json
{
    "properties": {
        "fullName": {
            "skip": [
                "this.firstName == null or this.lastName == null"
            ]
        }
    }
}
</code-block>
</tab>
<tab title="YAML">
<code-block lang="yaml">
# UserInfo.yaml
properties:
  friends:
    fullName:
      - "this.firstName == null or this.lastName == null"
</code-block>
</tab>
<tab title="NEON">
<code-block lang="neon">
# UserInfo.neon
properties:
  friends:
    fullName:
      - "this.firstName == null or this.lastName == null"
</code-block>
</tab>
</tabs>