# TypeStatement

A `TypeStatement` is an AST node that represents a complete and
independent description of a type. Each implementation of the type statement
contains one `$offset` property meaning the offset in bytes from the beginning
of the passed type definition string where the specified node is located.

<note>
You can use the position helper from the 
<a href="https://phplrt.org">phplrt library</a> to get full information (line 
and column) about the position.

<code-block lang="PHP">
use Phplrt\Position\Position;

$source = 'source code string';

$position = Position::fromOffset($source, $statement->offset);

echo \sprintf("line:   %d\n", $position->getLine());
echo \sprintf("column: %d\n", $position->getColumn());
echo \sprintf("offset: %d\n", $position->getOffset());
</code-block>
</note>


## Literals

Each literal implements class `TypeLang\Parser\Node\Literal\LiteralNode` and 
contains one public field `$raw` containing the string representation of the 
parsed data.

In addition, some literal nodes may contain parsed values of different 
types, which are located in the `$value` field.

Thus, if we parse, for example, `TrUe` string, the literal instance will contain:

```php
var_dump($literal->raw);   // string("TrUe")
var_dump($literal->value); // bool(true)
```

<note>
The examples omit the namespace prefix <code>TypeLang\Parser\Node\Literal</code>
for simplicity.
</note>

- `BoolLiteralNode` - Represents a `true` or `false` literal values
- `FloatLiteralNode` - Represents a float (i.e., `0.42`, `-1.2`, etc.) literal values
- `IntLiteralNode` - Represents an integer (i.e., `42`, `-12`, etc.) literal values
    - An integer values (<code>IntLiteralNode</code>) additionally contain a 
      <code>$decimal</code> property, which contains the string decimal value 
      of the parsed string
    <tip>
    This value can be used if the parsed value exceeds the allowed <code>PHP_INT_MAX</code>

    <code-block lang="php">
    // PHP does not support such literals and an overflow occurs
    // float(6.891571801898719E+28)
    var_dump(0xDEAD_BEEF_BAAD_F00D_8BAD_F00D);
    
    $literal = new \TypeLang\Parser\Parser()
        -&gt;parse('0xDEAD_BEEF_BAAD_F00D_8BAD_F00D');

    // string(&quot;0xDEAD_BEEF_BAAD_F00D_8BAD_F00D&quot;)
    var_dump($literal-&gt;raw);
    
    // The value exceeds the maximum allowed for 64-bit,
    // so the &quot;$literal-&gt;value&quot; contains PHP_INT_MAX
    // int(9223372036854775807)
    var_dump($literal-&gt;value);
    
    // Real decimal value in string format
    // string(&quot;68915718018987184226006420628&quot;)
    var_dump($literal-&gt;decimal);
    </code-block>
    </tip>
- `NullLiteralNode` - Represents `null` literal value
  - This node **does not** contain `$value` property
- `StringLiteralNode` - Represents any string (i.e., `'string'`, `"str\ning"`,
  etc.) literal values
- `VariableLiteralNode` - Represents any PHP variable (i.e., `$var`, `$this`, 
  etc.) literal values


## Named Types

Named types are a standalone type that contains a name. For example, a type 
declaration such as `int` is a named type, but `int|bool` is not, it is a 
union type of two named types.

Each named type contains one required property `$name` and two optional ones 
(can be `null`): 
- `$arguments` - List of type's template arguments
- `$fields` - List of type's shape fields

<compare first-title="Parse &quot;array&quot;" second-title="Data">
<code-block lang="PHP">
var_dump($stmt->name);
var_dump($stmt->arguments);
var_dump($stmt->fields);
</code-block>
<code-block lang="PHP">
string("array")
null
null
</code-block>
</compare>


<compare first-title="Parse &quot;array&lt;array-key, mixed>&quot;" second-title="Data">
<code-block lang="PHP">
var_dump($stmt->name);
var_dump($stmt->arguments);
var_dump($stmt->fields);
</code-block>
<code-block lang="PHP">
string("array")
object(TemplateArgumentsListNode)
null
</code-block>
</compare>


<compare first-title="Parse &quot;array{key: T}&quot;" second-title="Data">
<code-block lang="PHP">
var_dump($stmt->name);
var_dump($stmt->arguments);
var_dump($stmt->fields);
</code-block>
<code-block lang="PHP">
string("array")
null
object(FieldsListNode)
</code-block>
</compare>


<compare first-title="Parse &quot;array{key: T, ...&lt;T>}&quot;" second-title="Data">
<code-block lang="PHP">
var_dump($stmt->name);
var_dump($stmt->arguments);
var_dump($stmt->fields);
</code-block>
<code-block lang="PHP">
string("array")
object(TemplateArgumentsListNode)
object(FieldsListNode)
</code-block>
</compare>
