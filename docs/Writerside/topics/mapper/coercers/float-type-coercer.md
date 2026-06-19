# Float Coercion

<link-summary>
Cast passed value into a PHP float type.
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Type\Coercer\FloatTypeCoercer</code>
    </p>
    <p>
        Output Type:
        <code>float</code>
    </p>
</tldr>

Cast passed value into a PHP float type.

<table>
    <tr>
        <td>Input Value</td>
        <td>Output Result</td>
        <td>Description</td>
    </tr>
    <tr>
        <td>
            <code>float(T)</code>
        </td>
        <td>
            <code>float(T)</code>
        </td>
        <td>
            Returns float value &quot;as is&quot;
        </td>
    </tr>
    <tr>
        <td>
            <code>int(T)</code>
        </td>
        <td>
            <code>float(T)</code>
        </td>
        <td>
            Converts int to a float value
            <sup><a href="float-type-coercer.md#t1-p1">[1]</a></sup>
        </td>
    </tr>
    <tr>
        <td>
            <code>string(T)</code>
        </td>
        <td>
            <code>float(T)</code>
        </td>
        <td>
            Converts string to a float value
            <sup><a href="float-type-coercer.md#t1-p2">[2]</a></sup>
        </td>
    </tr>
    <tr>
        <td>
            <code>bool(false)</code>
        </td>
        <td>
            <code>float(0.0)</code>
        </td>
        <td>
        </td>
    </tr>
    <tr>
        <td>
            <code>bool(true)</code>
        </td>
        <td>
            <code>float(1.0)</code>
        </td>
        <td>
        </td>
    </tr>
    <tr>
        <td>
            <code>null</code>
        </td>
        <td>
            <code>float(0.0)</code>
        </td>
        <td>
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
        The int-to-float casts uses built-in PHP rules:
        <list>
            <li>
                The mantissa <code>0</code> will be added
            </li>
        </list>
    </step>    
    <step id="t1-p2">
        The string value must satisfy the following rules:
        <list>
            <li>
                Must be non-empty
            </li>
            <li>
                Must contain <a href="https://www.php.net/manual/en/function.is-numeric.php">numeric value</a>
            </li>
        </list>
    </step>
</procedure>