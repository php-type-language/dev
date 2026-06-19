# Array Key Coercion

<link-summary>
Cast passed value into a PHP "array key" type.
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Type\Coercer\ArrayKeyTypeCoercer</code>
    </p>
    <p>
        Output Type:
        <code>int|string</code>
    </p>
</tldr>

Cast passed value into a PHP "array key" type. An "array key" in PHP it can be
one of two types, either a `string` or an `int` number.

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
        <td>
            Returns string value &quot;as is&quot;
        </td>
    </tr>
    <tr>
        <td>
            <code>int(T)</code>
        </td>
        <td>
            <code>int(T)</code> 
        </td>
        <td>
            Returns int value &quot;as is&quot;
        </td>
    </tr>
    <tr>
        <td>
            <code>bool(false)</code>
        </td>
        <td>
            <code>int(0)</code>
        </td>
        <td></td>
    </tr>
    <tr>
        <td>
            <code>bool(true)</code>
        </td>
        <td>
            <code>int(1)</code>
        </td>
        <td></td>
    </tr>
    <tr>
        <td>
            <code>null</code>
        </td>
        <td>
            <code>int(0)</code>
        </td>
        <td></td>
    </tr>
    <tr>
        <td>
            <code>float(T)</code>
        </td>
        <td>
            <code>int(T)</code>
        </td>
        <td>
            Converts float to an int without losing precision
            <sup><a href="array-key-type-coercer.md#t1-p1">[1]</a></sup>
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
            Converts <code>Stringable</code> object to a string
            <sup><a href="array-key-type-coercer.md#t1-p2">[2]</a></sup>
        </td>
    </tr>
    <tr>
        <td>
            <code>BackedEnum</code>
        </td>
        <td>
            <code>string(BackedEnum::$value)</code>
        </td>
        <td>
            Returns the value of the backed enum's case
        </td>
    </tr>
    <tr>
        <td>
            <code>UnitEnum</code>
        </td>
        <td>
            <code>string(UnitEnum::$name)</code>
        </td>
        <td>
            Returns the name of the unit enum's case
        </td>
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
        The float value must satisfy the following rules:
        <list>
            <li>
                Must not be more than <code>PHP_INT_MAX</code>
            </li>
            <li>
                Must not be less than <code>PHP_INT_MIN</code>
            </li>
            <li>
                The mantissa should be <code>0</code> (should be no loss of 
                precision)
            </li>
        </list>
    </step>    
    <step id="t1-p2">
        For objects implementing the <code>Stringable</code> interface, the 
        <code>__toString()</code> method will be called.
    </step>
</procedure>
