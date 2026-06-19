# The Printer Component

<primary-label ref="printer-component"/>
<show-structure for="chapter" depth="2"/>

The printer package is responsible for visualizing the AST as string 
formats.

## Installation

<tldr>
    <p>
        Via <a href="https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies">Composer</a>:
        <code lang="bash">composer require type-lang/printer</code>
    </p>
</tldr>

**Requirements:**
* `PHP >= 8.1`

## Usage

Package supports two printer classes.

<deflist>
    <def title="TypeLang\Printer\PrettyPrinter">
        Used to display types as accurately as possible.
    </def>
    <def title="TypeLang\Printer\NativeTypePrinter">
        Used to display types compatible with PHP.
    </def>
</deflist>

Any printer implements the `TypeLang\Printer\PrinterInterface` interface, which 
contains an `print()` method for displaying ASTs as formatted strings.

### Shapes

<tabs>
<tab title="PrettyPrinter">

```php
$parser = new TypeLang\Parser\Parser();
$printer = new TypeLang\Printer\PrettyPrinter();

$result = $parser->parse(<<<'PHP'
    object{key: type, some: (list<T>|some<T>),
        ...<non-empty-string, int>}
    PHP);

echo $printer->print($result);
```

> Displays types as accurately as possible.
> ```php
> object{
>     key: type,
>     some: list<T>|some<T>,
>     ...<non-empty-string, int>
> }
> ```

</tab>
<tab title="NativeTypePrinter">

```php
$parser = new TypeLang\Parser\Parser();
$printer = new TypeLang\Printer\NativeTypePrinter();

$result = $parser->parse(<<<'PHP'
    object{key: type, some: (list<T>|some<T>),
        ...<non-empty-string, int>}
    PHP);

echo $printer->print($result);
```

> Displays types that are compatible with PHP.
> ```php
> object
> ```

</tab>
</tabs>

### Callables

<tabs>
<tab title="PrettyPrinter">

```php
$parser = new TypeLang\Parser\Parser();
$printer = new TypeLang\Printer\PrettyPrinter();

$result = $parser->parse(<<<'PHP'
    callable(...A &$a)|callable(B &...$b)
    PHP);

echo $printer->print($result);
```

> Displays types as accurately as possible.
> ```php
> callable(A &...$a)|callable(B &...$b)
> ```

</tab>
<tab title="NativeTypePrinter">

```php
$parser = new TypeLang\Parser\Parser();
$printer = new TypeLang\Printer\NativeTypePrinter();

$result = $parser->parse(<<<'PHP'
    callable(...A &$a)|callable(B &...$b)
    PHP);

echo $printer->print($result);
```

> Displays types that are compatible with PHP.
> ```php
> callable
> ```

</tab>
</tabs>

### Conditional

<tabs>
<tab title="PrettyPrinter">

```php
$parser = new TypeLang\Parser\Parser();
$printer = new TypeLang\Printer\PrettyPrinter();

$result = $parser->parse(<<<'PHP'
    $arg is null ? non-empty-string : int<0, max>
    PHP);

echo $printer->print($result);
```

> Displays types as accurately as possible.
> ```php
> ($arg is null ? non-empty-string : int<0, max>)
> ```

</tab>
<tab title="NativeTypePrinter">

```php
$parser = new TypeLang\Parser\Parser();
$printer = new TypeLang\Printer\NativeTypePrinter();

$result = $parser->parse(<<<'PHP'
    $arg is null ? non-empty-string : int<0, max>
    PHP);

echo $printer->print($result);
```

> Displays types that are compatible with PHP.
> ```php
> string|int
> ```

</tab>
</tabs>
