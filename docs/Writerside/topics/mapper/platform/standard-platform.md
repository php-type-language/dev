# Standard Platform

<show-structure for="chapter" depth="2"/>

<link-summary>
The standard platform is the most general platform, suitable 
for targets such as DB or API.
</link-summary>

<tldr>
    <p>
        Name: <code>standard</code>
    </p>
    <p>
        Class:
        <code>TypeLang\Mapper\Platform\StandardPlatform</code>
    </p>
</tldr>

<note>
The "standard" platform is used by default. No explicit declaration is required.
</note>

The standard platform is the most general platform, suitable
for targets such as DB or API.

If required, you can specify it explicitly, but this is required in cases 
where you want to [customize it](standard-platform.md#customization).

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Platform\StandardPlatform;

$mapper = new Mapper(new StandardPlatform());
```

## Customization

This platform supports customization of [metadata readers](meta-reader.md) and 
[metadata providers](meta-provider.md) (the `meta` constructor's argument). 
[Additional types](type-builders.md) (the `types` argument) 
and additional [type coercers](type-coercers.md) (the `coercers` argument) can 
be specified.


### Metadata Readers

For example, to customize a [metadata reader](meta-reader.md), you should pass
it explicitly to the `meta` parameter of the constructor.

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\Reader\AttributeReader;
use TypeLang\Mapper\Mapping\Reader\JsonConfigReader;
use TypeLang\Mapper\Mapping\Reader\YamlConfigReader;
use TypeLang\Mapper\Platform\StandardPlatform;

$attributes = new Mapper(new StandardPlatform(
    meta: new AttributeReader(),
));

$yamlConfigWithAttributes = new Mapper(new StandardPlatform(
    meta: new AttributeReader(
        delegate: new YamlConfigReader(...),
    ),
));
```

<tip>
You can read more about metadata readers in the 
<a href="meta-reader.md">"metadata readers" page</a>.
</tip>


### Metadata Providers

To modify [metadata provider](meta-provider.md), it can also be explicitly 
passed to the constructor in the `meta` argument.

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Mapping\Provider\InMemoryProvider;
use TypeLang\Mapper\Mapping\Provider\Psr16CacheProvider;
use TypeLang\Mapper\Mapping\Reader\AttributeReader;
use TypeLang\Mapper\Mapping\Reader\YamlConfigReader;
use TypeLang\Mapper\Platform\StandardPlatform;

$memoized = new Mapper(new StandardPlatform(
    meta: new InMemoryProvider(...),
));

$cacheAndMemoize = new Mapper(new StandardPlatform(
    meta: new InMemoryProvider(
        delegate: new Psr16CacheProvider(...),
    ),
));
```

<tip>
You can read more about metadata providers in the 
<a href="meta-provider.md">"metadata providers" page</a>.
</tip>


### Additional Types

To add [custom types](custom-type.md), pass a collection of 
[type builders](type-builders.md) to the `types` argument of the `StandardPlatform`
constructor. 

<note>
Passed types have higher priority than registered types
</note>

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Platform\StandardPlatform;

$extended = new Mapper(new StandardPlatform(types: [
    new My\Vendor\CustomTypeBuilder(),
    new My\Vendor\AnotherTypeBuilder(),
]);
```

<tip>
You can read more about types in the <a href="types.md">"types" page</a>.
</tip>

<tip>
You can read more about type builders in the <a href="type-builders.md">"type builders" page</a>.
</tip>


### Additional Coercers

To add [custom type coercers](custom-type-coercer.md), pass a collection of
[type coercers](type-coercers.md) to the `coercers` argument of the 
`StandardPlatform` constructor.

<note>
Passed type coercers have higher priority than registered
</note>

```php
use TypeLang\Mapper\Mapper;
use TypeLang\Mapper\Platform\StandardPlatform;
use TypeLang\Mapper\Type\StringType;

$extended = new Mapper(new StandardPlatform(coercers: [
    StringType::class => new My\Vendor\CustomStringTypeCoercer(),
]);
```

<tip>
You can read more about types in the <a href="types.md">"types" page</a>.
</tip>


## Types

The specified platform supports the following list of types.

<tabs>
<tab title="all">
    <snippet id="all-types">
        <table>
            <tr filter="alias,standalone">
                <td>Definition</td>
                <td filter="standalone" width="250">Class</td>
                <td filter="alias">Alias To</td>
                <td filter="standalone">Bidirectional</td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>array-key</code>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;ArrayKeyType
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>array</code>,<br />
                    <code>array&lt;K></code>,<br />
                    <code>array&lt;K,&nbsp;V></code>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;ArrayType
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>iterable</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>iterable&lt;K></code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array&lt;K></code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>iterable&lt;K,&nbsp;V></code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array&lt;K,&nbsp;V></code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>Iterator</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>Iterator&lt;K></code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array&lt;K></code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>Iterator&lt;K,&nbsp;V></code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array&lt;K,&nbsp;V></code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>Generator</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>Generator&lt;K></code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array&lt;K></code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>Generator&lt;K,&nbsp;V></code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array&lt;K,&nbsp;V></code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>Traversable</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>Traversable&lt;K></code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array&lt;K></code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>Traversable&lt;K,&nbsp;V></code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array&lt;K,&nbsp;V></code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>IteratorAggregate</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>IteratorAggregate&lt;K></code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array&lt;K></code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>IteratorAggregate&lt;K,&nbsp;V></code><sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>array&lt;K,&nbsp;V></code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>BackedEnum</code>
                    <sup><a href="standard-platform.md" anchor="t1-p1">[1]</a></sup>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;BackedEnumFromScalarType
                </td>
                <td filter="alias">~</td>
                <td>
                    <tooltip term="denormalize">
                        <kbd>D</kbd>
                    </tooltip>
                    <icon src="ko.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>BackedEnum</code>
                    <sup><a href="standard-platform.md" anchor="t1-p1">[1]</a></sup>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;BackedEnumToScalarType
                </td>
                <td filter="alias">~</td>
                <td>
                    <tooltip term="normalize">
                        <kbd>N</kbd>
                    </tooltip>
                    <icon src="ko.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>true</code>,<br/>
                    <code>false</code>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;BoolLiteralType
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>bool</code>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;<a href="bool-type.md">BoolType</a>
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>boolean</code>
                    <sup><a href="standard-platform.md" anchor="t1-p9">[9]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>bool</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>ClassName</code>
                    <sup><a href="standard-platform.md" anchor="t1-p2">[2]</a></sup>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;ClassFromArrayType
                </td>
                <td filter="alias">~</td>
                <td>
                    <tooltip term="denormalize">
                        <kbd>D</kbd>
                    </tooltip>
                    <icon src="ko.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>ClassName</code>
                    <sup><a href="standard-platform.md" anchor="t1-p2">[2]</a></sup>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;ClassToArrayType
                </td>
                <td filter="alias">~</td>
                <td>
                    <tooltip term="normalize">
                        <kbd>N</kbd>
                    </tooltip>
                    <icon src="ko.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>DateTime</code>,<br/>
                    <br/>
                    <code>DateTime&lt;T></code>
                    <sup>
                        <a href="standard-platform.md" anchor="t1-p3">3</a>,
                        <a href="standard-platform.md" anchor="t1-p4">4</a>
                    </sup>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;DateTimeFromStringType
                </td>
                <td filter="alias">~</td>
                <td>
                    <tooltip term="denormalize">
                        <kbd>D</kbd>
                    </tooltip>
                    <icon src="ko.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>DateTime</code>,<br/>
                    <code>DateTime&lt;T></code>
                    <sup><a href="standard-platform.md" anchor="t1-p3">[3]</a></sup>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;DateTimeToStringType
                </td>
                <td filter="alias">~</td>
                <td>
                    <tooltip term="normalize">
                        <kbd>N</kbd>
                    </tooltip>
                    <icon src="ko.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <i>float literal value</i> <sup><a href="standard-platform.md" anchor="t1-p5">[5]</a></sup>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;FloatLiteralType
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>float</code>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;FloatType
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>double</code>
                    <sup><a href="standard-platform.md" anchor="t1-p9">[9]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>float</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>real</code>
                    <sup><a href="standard-platform.md" anchor="t1-p9">[9]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>float</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="standalone">
                <td>
                    <i>int literal value</i> <sup><a href="standard-platform.md" anchor="t1-p6">[6]</a></sup>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;IntLiteralType
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>int&lt;min,&nbsp;max></code>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;IntRangeType
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>int</code>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;IntType
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>integer</code>
                    <sup><a href="standard-platform.md" anchor="t1-p9">[9]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>int</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>positive-int</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>int</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>non-positive-int</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>int</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>negative-int</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>int</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>non-negative-int</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>int</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>non-zero-int</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>int</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>number</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>int</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>numeric</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>int</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>list</code>,<br/>
                    <code>list&lt;V></code>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;ListFromArrayType
                </td>
                <td filter="alias">~</td>
                <td>
                    <tooltip term="denormalize">
                        <kbd>D</kbd>
                    </tooltip>
                    <icon src="ko.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>list</code>,<br/>
                    <code>list&lt;V></code>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;ListFromIterableType
                </td>
                <td filter="alias">~</td>
                <td>
                    <tooltip term="normalize">
                        <kbd>N</kbd>
                    </tooltip>
                    <icon src="ko.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>mixed</code>
                </td>
                <td>
                    <a href="mixed-type.md">MixedType</a>
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>?T</code>,<br/>
                    <code>T|null</code>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;NullableType
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>null</code>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;NullType
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>object</code>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;ObjectFromArrayType
                </td>
                <td filter="alias">~</td>
                <td>
                    <tooltip term="denormalize">
                        <kbd>D</kbd>
                    </tooltip>
                    <icon src="ko.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>object</code></td>
                <td>
                    <icon src="class.svg"/>&nbsp;ObjectToArrayType
                </td>
                <td filter="alias">~</td>
                <td>
                    <tooltip term="normalize">
                        <kbd>N</kbd>
                    </tooltip>
                    <icon src="ko.svg"/>
                </td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>stdClass</code>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>object</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="standalone">
                <td>
                    <i>string literal value</i> <sup><a href="standard-platform.md" anchor="t1-p7">[7]</a></sup>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;StringLiteralType
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>string</code>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;StringType
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>Stringable</code>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>non-empty-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>lowercase-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>non-empty-lowercase-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>uppercase-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>non-empty-uppercase-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>numeric-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>literal-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>non-empty-literal-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>class-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>interface-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>trait-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>enum-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>callable-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>truthy-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="alias">
                <td>
                    <code>non-falsy-string</code>
                    <sup><a href="standard-platform.md" anchor="t1-p10">[10]</a></sup>
                </td>
                <td filter="standalone">~</td>
                <td>
                    <code>string</code>
                </td>
                <td filter="standalone">~</td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>T|U</code>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;UnionType
                </td>
                <td filter="alias">~</td>
                <td>
                    <icon src="ok.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>UnitEnum</code>
                    <sup><a href="standard-platform.md" anchor="t1-p8">[8]</a></sup>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;UnitEnumFromStringType
                </td>
                <td filter="alias">~</td>
                <td>
                    <tooltip term="denormalize">
                        <kbd>D</kbd>
                    </tooltip>
                    <icon src="ko.svg"/>
                </td>
            </tr>
            <tr filter="standalone">
                <td>
                    <code>UnitEnum</code>
                    <sup><a href="standard-platform.md" anchor="t1-p8">[8]</a></sup>
                </td>
                <td>
                    <icon src="class.svg"/>&nbsp;UnitEnumToStringType
                </td>
                <td filter="alias">~</td>
                <td>
                    <tooltip term="normalize">
                        <kbd>N</kbd>
                    </tooltip>
                    <icon src="ko.svg"/>
                </td>
            </tr>
        </table>
    </snippet>
</tab>
<tab title="standalone">
    <include from="standard-platform.md"
        use-filter="!alias"
        element-id="all-types" />
</tab>
<tab title="aliases">
    <include from="standard-platform.md"
        use-filter="!standalone"
        element-id="all-types" />
</tab>
</tabs>

<procedure title="Notes" collapsible="true">
    <step id="t1-p1">
        In case of the backed (<code>int</code> or <code>string</code>) enum is
        registered:
        <list>
            <li>
                <p>
                    <code>enum BackedEnum: string { ... }</code>
                </p>
            </li>
            <li>
                <p>
                    <code>enum BackedEnum: int { ... }</code>
                </p>
            </li>
        </list>
    </step>
    <step id="t1-p2">
        In case of the class is registered:
        <list>
            <li>
                <p>
                    <code>class ClassName { ... }</code>
                </p>
            </li>
        </list>
    </step>
    <step id="t1-p3">
        Any implementation of the <code>DateTimeInterface</code>
    </step>
    <step id="t1-p4">
        When specifying the <code>DateTimeInterface</code> as the type, an 
        instance of <code>DateTimeImmutable</code> will be created.
    </step>
    <step id="t1-p5">
        A <a href="literal-types.md#float">literal float value</a> 
        is an arbitrary float, such as:
        <list>
            <li><code>15.16</code></li>
            <li><code>23.</code></li>
            <li><code>-.42</code></li>
            <li>etc.</li>
        </list>
    </step>
    <step id="t1-p6">
        A <a href="literal-types.md#integer">literal int value</a> 
        is an arbitrary int, such as:
        <list>
            <li><code>23</code></li>
            <li><code>0xDEAD_BEEF</code></li>
            <li>etc.</li>
        </list>
    </step>
    <step id="t1-p7">
        A <a href="literal-types.md#strings">literal string value</a> 
        is an arbitrary string, such as:
        <list>
            <li><code>"example string"</code></li>
        </list>
    </step>
    <step id="t1-p8">
        In case of the unit enum is registered:
        <list>
            <li>
                <p>
                    <code>enum UnitEnum { ... }</code>
                </p>
            </li>
        </list>
    </step>
    <step id="t1-p9">
        Non-canonical alias; Means that such naming is incorrect and recommended
        to rename the type to original
    </step>
    <step id="t1-p10">
        Temporary alias; Means that at the moment this type is an alias and does
        not carry any additional logic, but this may change in the future.
    </step>
</procedure>


<tip>
You can read more about builders in the 
<a href="type-builders.md">"type builders" page</a>.

More information about the types themselves can be found in
<a href="types.md">"types" page</a>.
</tip>

## Type Coercions

The specified platform supports the following list of types coercers.

<table>
    <tr>
        <td width="200">Class</td>
        <td>Applies To</td>
        <td>Used In</td>
    </tr>
    <tr>
        <td>
            <icon src="class.svg" />
            <a href="array-key-type-coercer.md">ArrayKeyTypeCoercer</a>
        </td>
        <td>
            <code>array-key</code>
        </td>
        <td>
            <code>array</code>
        </td>
    </tr>
    <tr>
        <td>
            <icon src="class.svg" />
            <a href="bool-type-coercer.md">BoolTypeCoercer</a>
        </td>
        <td>
            <code>bool</code>,<br />
            <code>true</code>,<br />
            <code>false</code>
        </td>
        <td>~</td>
    </tr>
    <tr>
        <td>
            <icon src="class.svg" />
            <a href="float-type-coercer.md">FloatTypeCoercer</a>
        </td>
        <td>
            <code>float</code>,<br />
            <i>float&nbsp;literal&nbsp;value</i>
            <sup><a href="standard-platform.md" anchor="t2-p1">[1]</a></sup>
        </td>
        <td>~</td>
    </tr>
    <tr>
        <td>
            <icon src="class.svg" />
            <a href="int-type-coercer.md">IntTypeCoercer</a>
        </td>
        <td>
            <code>int</code>,<br />
            <code>int&lt;min,&nbsp;max&gt;</code>,<br />
            <i>int&nbsp;literal&nbsp;value</i>
            <sup><a href="standard-platform.md" anchor="t2-p2">[2]</a></sup>
        </td>
        <td>
            <code>array-key</code>,<br />
            <code>BackedEnum&lt;int&gt;</code>
        </td>
    </tr>
    <tr>
        <td>
            <icon src="class.svg" />
            <a href="string-type-coercer.md">StringTypeCoercer</a>
        </td>
        <td>
            <code>string</code>,<br />
            <i>string&nbsp;literal&nbsp;value</i>
            <sup><a href="standard-platform.md" anchor="t2-p3">[3]</a></sup>
        </td>
        <td>
            <code>array-key</code>,<br />
            <code>BackedEnum&lt;string&gt;</code>,<br />
            <code>UnitEnum</code>,<br />
            <code>DateTime</code>
        </td>
    </tr>
</table>

<procedure title="Notes" collapsible="true">
    <step id="t2-p1">
        A <a href="literal-types.md#float">literal float value</a> 
        is an arbitrary float, such as:
        <list>
            <li><code>15.16</code></li>
            <li><code>23.</code></li>
            <li><code>-.42</code></li>
            <li>etc.</li>
        </list>
    </step>
    <step id="t2-p2">
        A <a href="literal-types.md#integer">literal int value</a> 
        is an arbitrary int, such as:
        <list>
            <li><code>23</code></li>
            <li><code>0xDEAD_BEEF</code></li>
        </list>
    </step>
    <step id="t2-p3">
        A <a href="literal-types.md#strings">literal string value</a> 
        is an arbitrary string, such as:
        <list>
            <li><code>"example string"</code></li>
        </list>
    </step>
</procedure>

<tip>
You can read more about coercers in the 
<a href="type-coercers.md">"type coercers" page</a>.
</tip>

