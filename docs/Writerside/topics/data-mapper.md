# Data Mapper

<primary-label ref="mapper-component" xmlns=""/>
<show-structure for="chapter" depth="2"/>

A mapper is a system that transforms internal data into external data 
(normalization) and vice versa (denormalization) using a set of declarative
transformation rules.

A mapper can be used in the ACL layer (anti-corruption) for transforming DTOs 
or for interacting with the database (converting primitive data into full-fledged
entities and value objects). It can also be used for serialization and 
deserialization of data in command or query buses. It can also be used to 
transform JSON data in APIs into full-fledged DTOs, and much more.

## Installation

<tldr>
    <p>
        Via <a href="https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies">Composer</a>:
        <code lang="bash">composer require type-lang/mapper</code>
    </p>
</tldr>

**Requirements:**
* `PHP >= 8.1`
* `ext-pcre`

## Usage

To create a mapper, you should use instantiation of the `TypeLang\Mapper\Mapper` 
object, after which the code is ready to use.

```php
$mapper = new TypeLang\Mapper\Mapper();
```

The mapper contains two transformation directions:
- <tooltip term="normalize">**Normalization**</tooltip> - the process of 
  converting complex internal application data to external data (API, 
  database, etc.)
  <code-block lang="PHP">
  $mapper = new TypeLang\Mapper\Mapper();

  $result = $mapper->normalize(new Example());
  </code-block>
- <tooltip term="denormalize">**Denormalization**</tooltip> - the process of 
  converting simple external data to internal application data types
  <code-block lang="PHP">
    $mapper = new TypeLang\Mapper\Mapper();

    $result = $mapper->denormalize($data, Example::class);
  </code-block>

As you can see, the <tooltip term="denormalize">denormalization process</tooltip> 
requires information about the type to which the incoming data will be converted.

With <tooltip term="normalize">normalization</tooltip>, the type is optional, 
as it can be [determined automatically](type-extractors.md) based on the 
incoming data, but can also be explicitly specified as a second argument.

```php
$mapper->normalize(new Example(), Example::class);
```

The type specified in the second argument of the
<tooltip term="normalize">normalization</tooltip> and
<tooltip term="denormalize">denormalization</tooltip> methods can be arbitrary,
as described in the [language grammar](introduction.md) section.

An example of this "type definition" is shown below. However, the capabilities 
(and existence) of this type depend on [the platform](mapper-platforms.md).

```php
$result = $mapper->normalize(new Example(), <<<'PHP'
    object{
        field: non-empty-string,
        another: int<0, max>,
    }
    PHP);
```

The types themselves, their availability, capabilities, functionality and 
behavior depend on the selected platform.

<note>
The general <a href="standard-platform.md"><b>standard</b> platform</a> 
one is used by default
</note>


## Customization

The `Mapper` can also take several additional arguments.

```php
$mapper = new TypeLang\Mapper\Mapper(
    [[[platform: ...,|mapper-platforms.md]]]
    [[[config: ...,|mapper-configuration.md]]]
    [[[typeExtractorFactory: ...,|type-extractors.md]]]
    typeParserFactory: ...,
    typeRepositoryFactory: ...,
);
```

- `platform` - Allows to specify a set of specific types, type coercers, 
  and syntax rules
  <tip>For more details, see the 
    <a href="mapper-platforms.md">"platform" documentation</a></tip>
- `config` - Allows to specify a custom configuration
  <tip>For more details, see the 
    <a href="mapper-configuration.md">"configuration" documentation</a></tip>
- `typeExtractorFactory` - Allows to specify custom rules for inferring types 
  from values
  <tip>For more details, see the 
    <a href="type-extractors.md">"type extractors" documentation</a></tip>
- `typeParserFactory` - Allows to specify custom type parsing rules
- `typeRepositoryFactory` - Allows to specify a custom implementation of 
  the type registry (repository)