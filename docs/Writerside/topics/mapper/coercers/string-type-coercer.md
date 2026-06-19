# String Coercion

<link-summary>
Cast passed value into a PHP string type.
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Type\Coercer\StringTypeCoercer</code>
    </p>
    <p>
        Output Type:
        <code>string</code>
    </p>
</tldr>

Cast passed value into a PHP string type.

The `StringTypeCoercer` takes one optional `floatPrecision` parameter.

This parameter responsible for maximum precision when converting from float 
to string. If the value is not specified (`null`), then the precision corresponds
to the [value of `precision`](https://www.php.net/manual/en/ini.core.php#ini.precision)
in `php.ini`.

```php
$coercer = new TypeLang\Mapper\Type\Coercer\StringTypeCoercer(
    // Expects: int<1, 53>|null
    floatPrecision: 42,
);
```

<table>
    <tr>
        <td>Input Value</td>
        <td>Output Result</td>
        <td>Description</td>
    </tr>
    <tr>
        <td>
            <code>string(T)</code>
        </td>
        <td>
            <code>string(T)</code>
        </td>
        <td>Returns the string value &quot;as is&quot;</td>
    </tr>
    <tr>
        <td>
            <code>null</code>
        </td>
        <td>
            <code>string(&quot;&quot;)</code>
        </td>
        <td>
        </td>
    </tr>
    <tr>
        <td>
            <code>bool(true)</code>
        </td>
        <td>
            <code>string(&quot;true&quot;)</code>
        </td>
        <td>
        </td>
    </tr>
    <tr>
        <td>
            <code>bool(false)</code>
        </td>
        <td>
            <code>string(&quot;false&quot;)</code>
        </td>
        <td>
        </td>
    </tr>
    <tr>
        <td>
            <code>float(INF)</code>
        </td>
        <td>
            <code>string(&quot;inf&quot;)</code>
        </td>
        <td>
        </td>
    </tr>
    <tr>
        <td>
            <code>float(-INF)</code>
        </td>
        <td>
            <code>string(&quot;-inf&quot;)</code>
        </td>
        <td>
        </td>
    </tr>
    <tr>
        <td>
            <code>float(NAN)</code>
        </td>
        <td>
            <code>string(&quot;nan&quot;)</code>
        </td>
        <td>
        </td>
    </tr>
    <tr>
        <td>
            <code>float(T)</code>
        </td>
        <td>
            <code>string(T)</code>
        </td>
        <td>
            Converts float to a string value
            <sup><a href="string-type-coercer.md#t1-p1">[1]</a></sup>
        </td>
    </tr>
    <tr>
        <td>
            <code>int(T)</code>
        </td>
        <td>
            <code>string(T)</code>
        </td>
        <td>
            Converts int to a string value
            <sup><a href="string-type-coercer.md#t1-p2">[2]</a></sup>
        </td>
    </tr>
    <tr>
        <td>
            <code>Stringable</code>
        </td>
        <td>
            <code>string(T)</code>
        </td>
        <td>
            Converts object to a string value
            <sup><a href="string-type-coercer.md#t1-p3">[3]</a></sup>
        </td>
    </tr>
    <tr>
        <td>
            <code>resource(T)</code>
        </td>
        <td>
            <code>string(T)</code>
        </td>
        <td>
            Converts resource to a string value
            <sup><a href="string-type-coercer.md#t1-p4">[4]</a></sup>
        </td>
    </tr>
    <tr>
        <td>
            <code>BackedEnum</code>
        </td>
        <td>
            <code>string(BackedEnum::$value)</code>
        </td>
        <td>Returns the value of the backed enum's case</td>
    </tr>
    <tr>
        <td>
            <code>UnitEnum</code>
        </td>
        <td>
            <code>string(UnitEnum::$name)</code>
        </td>
        <td>Returns the name of the unit enum's case</td>
    </tr>
    <tr>
        <td>Other</td>
        <td>
            <code>never</code>
        </td>
        <td>
            Throws <code>InvalidValueException</code> exception
        </td>
    </tr>
</table>


<procedure title="Notes" collapsible="true">
    <step id="t1-p1">
        The float-to-string casting uses built-in PHP rules:
        <list>
            <li>
                The float value accuracy depends on the
                <a href="https://www.php.net/manual/en/ini.core.php#ini.precision"><code>precision</code> value</a>
                in <code>php.ini</code> file.
            </li>
            <li>
                If a loss of precision occurs during conversion to a string, an 
                <code>InvalidValueException</code> error will occur.
            </li>
        </list>
    </step>
    <step id="t1-p2">
        The int-to-string casting uses built-in PHP rules.
    </step>
    <step id="t1-p3">
        For objects implementing the <code>Stringable</code> interface, 
        the <code>__toString()</code> method will be called.
    </step>
    <step id="t1-p4">
        Returns the resource name, such as <code>&quot;stream&quot;</code> for 
        file streams. If the resource is unknown, then simply the string 
        <code>&quot;resource&quot;</code> will be returned.
    </step>
</procedure>