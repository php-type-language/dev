# Integer Coercion

<link-summary>
Cast passed value into a PHP int type.
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Type\Coercer\IntTypeCoercer</code>
    </p>
    <p>
        Output Type:
        <code>int</code>
    </p>
</tldr>

Cast passed value into a PHP int type.

<table>
    <tr>
        <td>Input Value</td>
        <td>Output Result</td>
        <td>Description</td>
    </tr>
    <tr>
        <td>
            <code>int(T)</code>
        </td>
        <td>
            <code>int(T)</code>
        </td>
        <td>Returns int value &quot;as is&quot;</td>
    </tr>
    <tr>
        <td>
            <code>BackedEnum&lt;int&gt;</code>
        </td>
        <td>
            <code>int(BackedEnum&lt;int&gt;::$value)</code>
        </td>
        <td>Returns the value of the int backed enum case</td>
    </tr>
    <tr>
        <td>
            <code>string(T)</code>
        </td>
        <td>
            <code>int(T)</code>
        </td>
        <td>
            Converts string to an integer value
            <sup><a href="int-type-coercer.md#t1-p1">[1]</a></sup>
        </td>
    </tr>
    <tr>
        <td>
            <code>float(T)</code>
        </td>
        <td>
            <code>int(T)</code>
        </td>
        <td>
            Converts float to an integer value
            <sup><a href="int-type-coercer.md#t1-p2">[2]</a></sup>
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
        <td>Other</td>
        <td><code>never</code></td>
        <td>
            Throws <code>InvalidValueException</code> exception
        </td>
    </tr>
</table>

<procedure title="Notes" collapsible="true">
    <step id="t1-p1">
        The string value must satisfy the following rules:
        <list>
            <li>
                Must be non-empty
            </li>
            <li>
                Must contain integer-like <a href="https://www.php.net/manual/en/function.is-numeric.php">numeric value</a> 
                or must float-like allowing casting without loss of accuracy
                <sup><a href="int-type-coercer.md#t1-p2">[2]</a></sup>
            </li>
        </list>
    </step>    
    <step id="t1-p2">
        The float value must satisfy the following rules:
        <list>
            <li>
                Must not be more than <code>PHP_INT_MAX</code>
            </li>
            <li>
                Must not be less than <code>PHP_INT_MIN</code>
            </li>
            <li>
                The mantissa should be <code>0</code> (should be no loss of precision)
            </li>
        </list>
    </step>
</procedure>