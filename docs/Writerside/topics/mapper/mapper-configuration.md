# Configuration

Configuration is a `TypeLang\Mapper\Configuration` DTO passed to the
`TypeLang\Mapper\Mapper` constructor, containing a list of general mapper
settings.

```php
use Psr\Log\LoggerInterface;
use TypeLang\Mapper\Runtime\Tracing\TracerInterface;

$mapper = new TypeLang\Mapper\Mapper(
    config: new TypeLang\Mapper\Configuration(
        // options = true|false|null
        // default = null
        objectAsArray: null,

        // options = true|false|null
        // default = null
        strictTypes: null,

        // options = true|false
        // default = false
        logTypeParse: false,

        // options = true|false
        // default = false
        logTypeFind: false,

        // options = true|false
        // default = true
        logTypeMatch: true,

        // options = true|false
        // default = true
        logTypeCast: true,

        // options = LoggerInterface|null
        // default = null
        logger: null,

        // options = true|false
        // default = false
        traceTypeParse: false,

        // options = true|false
        // default = false
        traceTypeFind: false,

        // options = true|false
        // default = true
        traceTypeMatch: true,

        // options = true|false
        // default = true
        traceTypeCast: true,

        // options = TracerInterface|null
        // default = null
        tracer: null,
    ),
);
```

## Object As Array

This option is responsible for converting user defined objects 
PHP into associative arrays.

<note>
This option is only applicable if the output can be an array or an object.
For example, in the <a href="standard-platform.md">standard platform</a>.
</note>

<warning>
If the configuration value is not set, the option will
be <b>enabled</b>:
<list>
<li><code>objectAsArray: true</code></li>
</list>
</warning>

<tabs>
<tab title="enabled (true)">
<code-block lang="php">
<![CDATA[
$config = new Configuration(objectAsArray: true);

$result = new Mapper(config: $config)
    ->normalize((object)['key' => 'value']);

// array:1 [
//   "key" => "value"
// ]
]]>
</code-block>
</tab>
<tab title="disabled (false)">
<code-block lang="php">
<![CDATA[
$config = new Configuration(objectAsArray: false);

$result = new Mapper(config: $config)
    ->normalize((object)['key' => 'value']);

// object(StdClass) {
//   "key": "value"
// }
]]>
</code-block>
</tab>
</tabs>

## Strict Types

This option is responsible for disabling the strict comparison 
mode and attempts to coerce (cast) the values.

<tip>
When this option is enabled, the corresponding cast rules 
defined in the <a href="type-coercers.md">type coercers</a> are enabled.
</tip>

<warning>
If the configuration value is not set, the option will
be <b>disabled</b>:
<list>
<li><code>strictTypes: false</code></li>
</list>
</warning>


<tabs>
<tab title="enabled (true)">
<code-block lang="php">
<![CDATA[
$config = new Configuration(strictTypes: true);

$result = new Mapper(config: $config)
    ->normalize(['k' => 42], 'list<string>');

// TypeLang\Mapper\Exception\Mapping\InvalidIterableValueException:
// Passed value 42 on "k" in {"k": 42} is invalid at $[0]
//

$result = new Mapper(config: $config)
    ->denormalize(['k' => 42], 'list<string>');

// TypeLang\Mapper\Exception\Mapping\InvalidValueException:
// Passed value {"k": 42} is invalid
//
]]>
</code-block>
</tab>
<tab title="disabled (false)">
<code-block lang="php">
<![CDATA[
$config = new Configuration(strictTypes: false);

$result = new Mapper(config: $config)
    ->normalize(['k' => 42], 'list<string>');

// array:1 [
//   0 => "42"
// ]

$result = new Mapper(config: $config)
    ->denormalize(['k' => 42], 'list<string>');

// array:1 [
//   0 => "42"
// ]
]]>
</code-block>
</tab>
</tabs>