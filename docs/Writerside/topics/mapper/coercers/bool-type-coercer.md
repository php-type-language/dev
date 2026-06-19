# Bool Coercion

<link-summary>
Cast passed value into a PHP boolean type.
</link-summary>

<tldr>
    <p>
        Class:
        <code>TypeLang\Mapper\Type\Coercer\BoolTypeCoercer</code>
    </p>
    <p>
        Output Type:
        <code>bool</code>
    </p>
</tldr>

Cast passed value into a PHP boolean type.

| Input Value   | Output Result |
|---------------|---------------|
| `bool(false)` | `bool(false)` |
| `string("")`  | `bool(false)` |
| `string("0")` | `bool(false)` |
| `array([])`   | `bool(false)` |
| `null`        | `bool(false)` |
| `int(0)`      | `bool(false)` |
| `float(0.0)`  | `bool(false)` |
| Other         | `bool(true)`  |

<note>
This type coercer does not cause type errors. All values not provided
by the type coercer will be cast to the `bool(true)`.
</note>

<warning>
Note that the behavior deviates from PHP's built-in casts in 
favor of explicitness.

This means that the behavior of `(bool) $value`, `filter_var($value, FILTER_VALIDATE_BOOLEAN)` 
and the rules from `BoolTypeCoercer` will be different.

<code-block lang="PHP">
$value = new \SimpleXMLElement('&lt;xml />');

// Built-in PHP behaviour:
(bool) $value;
// bool(false)

// Built-in "ext-filter" behaviour:
filter_var($value, FILTER_VALIDATE_BOOLEAN); 
// bool(false)

// Bool coercer behaviour:
$coercer->coerce($value);
// bool(true)
</code-block>
</warning>