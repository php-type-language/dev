# Syntax Comparison

<show-structure for="chapter" depth="2"/>

The language is defined by a syntax that is based on the grammar of popular
static code analysis tools: [PHPStan](https://phpstan.org/) and [Psalm](https://psalm.dev/).

- PHPStan: [https://phpstan.org](https://phpstan.org/writing-php-code/phpdoc-types)
- Psalm: [https://psalm.dev](https://psalm.dev/docs/annotating_code/type_syntax/atomic_types/)

<tip>
There are others that are not currently tested for compatibility. 
For example:
<list>
    <li>PHAN: <a href="https://github.com/phan/phan/wiki">https://github.com/phan</a></li>
    <li>phpDocumentor: <a href="https://docs.phpdoc.org/guide/guides/types.html">https://docs.phpdoc.org</a></li>
</list>

If you wish, you can add information about this software using a
<a href="https://github.com/php-type-language/docs/blob/master/Writerside/topics/language.md">
    pull request to the documentation
</a>.
</tip>


> Below is a comparison list of all syntactic structures (grammar).
> The logical component (the physical existence of the type) is **NOT**
> taken into account.

General table across all type parsing capabilities

<table style="header-row">
    <tr>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan">PHPStan</a>
        </td>
    </tr>
    <tr>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">62/62</format>
            </note>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">42/62</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">46/62</format>
            </warning>
        </td>
    </tr>
</table>

## Basic Types

Below is a list of simple, logical and other common types.

<table style="both">
    <tr>
        <td width="1"></td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan">PHPStan</a>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">11/12</format>
            </note>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">9/12</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">10/12</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="basic-types.md" anchor="namespace">
                Class or type name (including <tooltip term="FQN">FQN</tooltip>)
            </a>
            <code-block lang="typescript">
            Fully\Qualified\Name
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="logical-types.md" anchor="union-types">
                Logical union types
            </a>
            <code-block lang="typescript">
            T | U | V
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="logical-types.md" anchor="intersection-types">
                Logical intersection types
            </a>
            <code-block lang="typescript">
            T & U & V
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="logical-types.md" anchor="nullable-types">
                Logical nullable types
            </a>
            <code-block lang="typescript">
            ?T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="generic-types.md" anchor="list-syntax">
                Legacy list types syntax
            </a>
            <code-block lang="typescript">
                User[]
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="generic-types.md">
                Template arguments (Generics)
            </a>
            <code-block lang="typescript">
                ExampleCollection&lt;array-key, User>
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="warning.svg"/>
            <a anchor="ref-1-1">
                Trailing comma not supported <sup>1</sup>
            </a>
        </td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm does NOT support arguments ending with a comma" id="ref-1-1">
                    <code-block lang="typescript">
                    ExampleCollection&lt;array-key, User,>
                    </code-block>
                    <a href="https://psalm.dev/r/866c32c49d">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="generic-types.md" anchor="call-site-hints">
                Template argument hints
            </a>
            <code-block lang="typescript">
                ExampleCollection&lt;in array-key, out User>
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/80a466e81c">
                Not Supported
            </a>
        </td>
        <td>
            <icon src="ok.svg"/>
            <a anchor="ref-1-2">
                Call-site variance <sup>1</sup>
            </a>
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. PHPStan supports call-site variance" id="ref-1-2">
                    <code-block lang="typescript">
                    Collection&lt;covariant Animal>
                    </code-block>
                    <a href="https://phpstan.org/blog/whats-up-with-template-covariant#call-site-variance">Open in phpstan.org</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="generic-types.md" anchor="attributes">
                Template argument attributes
            </a>
            <code-block lang="typescript">
                ExampleCollection&lt;#[assert(not&lt;"0">)] array-key>
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            Not Supported
        </td>
        <td>
            <icon src="ko.svg"/>
            Not Supported
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="const-types.md" anchor="class-constants">
                Class constant types
            </a>
            <code-block lang="typescript">
                ClassName::CONSTANT_NAME
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="const-types.md" anchor="constant-masks">
                Prefixed class constant mask types
            </a>
            <code-block lang="typescript">
                ClassName::CONSTANT_*
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="const-types.md" anchor="constant-masks">
                Non-prefixed class constant mask types
            </a>
            <code-block lang="typescript">
                ClassName::*
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="const-types.md" anchor="constant-masks">
                Global constant mask types
            </a>
            <code-block lang="typescript">
                JSON_*
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/2872401a31">
                Not Supported
            </a>
        </td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/7705c6cc-4aad-4394-8f54-0bdae761193e">
                Not Supported
            </a>
        </td>
    </tr>
</table>

## Conditional Types

Below is a list of conditional types.

<table style="both">
    <tr>
        <td width="1"></td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan">PHPStan</a>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">6/6</format>
            </note>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">4/6</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">4/6</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="conditional-types.md">
                Conditional positive equality types
            </a>
            <code-block lang="typescript">
            T is A ? B : C
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="conditional-types.md">
                Conditional negative equality types
            </a>
            <code-block lang="typescript">
            T is not A ? B : C
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="conditional-types.md">
                Conditional referenced types
            </a>
            <code-block lang="typescript">
            $var is A ? B : C
            $var is not A ? B : C
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="conditional-types.md">
                Conditional referenced types 
            </a>
            <a href="https://en.wikipedia.org/wiki/Yoda_conditions">
                in Yoda-style
            </a>
            <code-block lang="typescript">
            A is $var ? B : C
            A is not $var ? B : C
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/c70473ea70">
                Not Supported
            </a>
        </td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/dc886f85-85b6-46b4-9a21-a37a90e6b0c9">
                Not Supported
            </a>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="conditional-types.md">
                Functions in conditional types
            </a>
            <code-block lang="typescript">
            foo() is A ? B : C
            foo() is not A ? B : C
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/> <a anchor="ref-2-1">List of supported functions <sup>1</sup></a></td>
        <td><icon src="ok.svg"/> <a anchor="ref-2-2">List of supported functions <sup>2</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm supports the following functions" id="ref-2-1">
                    <list>
                        <li><code>define()</code></li>
                        <li><code>array_map()</code></li>
                        <li><code>array_filter()</code></li>
                        <li><code>func_get_arg()</code></li>
                        <li><code>func_get_args()</code></li>
                        <li><code>func_num_args()</code></li>
                        <li><code>is_a()</code></li>
                        <li><code>is_subclass_of()</code></li>
                        <li><code>class_alias()</code></li>
                    </list>
                </def>
                <def title="2. PHPStan supports the following functions" id="ref-2-2">
                    <list>
                        <li><code>func_get_arg()</code></li>
                        <li><code>func_get_args()</code></li>
                        <li><code>func_num_args()</code></li>
                    </list>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="conditional-types.md">
                Functions in conditional types
            </a>
            <a href="https://en.wikipedia.org/wiki/Yoda_conditions">
                in Yoda-style
            </a>
            <code-block lang="typescript">
            A is foo() ? B : C
            A is not foo() ? B : C
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/d936b5ed48">
                Not Supported
            </a>
        </td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/529ccac7-2c9b-4a59-837f-26c846bd216f">
                Not Supported
            </a>
        </td>
    </tr>
</table>

## Literal Types

Below is a list of literal types/lexemes.

<table style="both">
    <tr>
        <td width="1"></td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan">PHPStan</a>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">19/19</format>
            </note>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">7/19</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">11/19</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="boolean-and-null">
                Boolean <code>true</code> and <code>false</code> literals
            </a>
            <code-block lang="typescript">
            true
            false
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="boolean-and-null">
                The <code>null</code> literals
            </a>
            <code-block lang="typescript">
            null
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="strings">
                Single-quoted string literals
            </a>
            <code-block lang="typescript">
            'single-quoted string'
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="strings">
                Double-quoted string literals
            </a>
            <code-block lang="typescript">
            "double-quoted string"
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="escape-sequences">
                Escape sequences in a double-quoted string literals
            </a>
            <code-block lang="typescript">
            "string with \n new line \n delimiters"
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/a4763e39ea">
                Not Supported
            </a>
        </td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/ef392d41-f4e5-474c-8426-4ecdc583080a">
                Not Supported
            </a>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="hexadecimal-sequences">
                Hexadecimal sequences in a double-quoted string literals
            </a>
            <code-block lang="typescript">
            "\xDE\xAD\xBE\xEF"
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/ce7cdf12ba">
                Not Supported
            </a>
        </td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/06c7f670-4db4-433b-b181-d3c8b7219980">
                Not Supported
            </a>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="unicode-sequences">
                Unicode sequences in a double-quoted string literals
            </a>
            <code-block lang="typescript">
            "This is smile \u{1F60A}"
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/73412b8746">
                Not Supported
            </a>
        </td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/ebfdf3b6-e8e2-413d-adc5-a56ddd564bab">
                Not Supported
            </a>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="integer">
                Integer literals
            </a>
            <code-block lang="typescript">
            42
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="integer">
                BigInteger (<code>PHP_INT_MAX + 1</code> or <code>PHP_INT_MIN - 1</code>) literals
            </a>
            <code-block lang="typescript">
            42
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="warning.svg"/> <a anchor="ref-3-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="warning.svg"/> <a anchor="ref-3-2">Works with restrictions <sup>2</sup></a></td>
        <td><icon src="warning.svg"/> <a anchor="ref-3-3">Works with restrictions <sup>3</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. TypeLang limits value to min/max int and store literal value as string" id="ref-3-1">
                    <code-block lang="typescript">
                    // Input
                    9999999999999999999
                    </code-block>
                    <code-block lang="typescript">
                    // Stored
                    TypeLang\Parser\Node\Literal\IntLiteralNode {
                      +offset: 0
                      +raw: "9999999999999999999"
                      +value: 9223372036854775807
                    }
                    </code-block>
                </def>
                <def title="2. Psalm limits value to min/max int" id="ref-3-2">
                    <code-block lang="typescript">
                    // Input
                    9999999999999999999
                    </code-block>
                    <code-block lang="typescript">
                    // Stored
                    9223372036854775807
                    </code-block>
                    <a href="https://psalm.dev/r/6136de9980">Open in psalm.dev</a>
                </def>
                <def title="3. PHPStan limits value to min/max int" id="ref-3-3">
                    <code-block lang="typescript">
                    // Input
                    9999999999999999999
                    </code-block>
                    <code-block lang="typescript">
                    // Stored
                    9223372036854775807
                    </code-block>
                    <a href="https://phpstan.org/r/e8f6fef0-7726-412a-9c99-9eef04c53c17">Open in phpstan.org</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="binary">
                Integer literals in binary format
            </a>
            <code-block lang="typescript">
            0b10101010
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/75794af443">
                Not Supported
            </a>
        </td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/79283030-f55e-4eb1-8b6b-2bdbc4083d30">
                Not Supported
            </a>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="octal">
                Integer literals in octal format
            </a>
            <code-block lang="typescript">
            0o42
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/8552461d46">
                Not Supported
            </a>
        </td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/362869d4-5b65-441c-8708-f9f32993b560">
                Not Supported
            </a>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="octal">
                Integer literals in legacy octal format
            </a>
            <code-block lang="typescript">
            042
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/e4ab56c714">
                Not Supported
            </a>
        </td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/20f18b17-94c8-403c-8ad7-14058eb8a0ef">
                Not Supported
            </a>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="hexadecimal">
                Integer literals in hexadecimal format
            </a>
            <code-block lang="typescript">
            0xDEAD_BEEF
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/60176a85f4">
                Not Supported
            </a>
        </td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/f9fcaaa6-384e-4d58-b38c-8a51f091abf8">
                Not Supported
            </a>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="float">
                Float literals
            </a>
            <code-block lang="typescript">
            0.42
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="float">
                Float literals without leading zero
            </a>
            <code-block lang="typescript">
            .42
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/816ae7db23">
                Not Supported
            </a>
        </td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="float">
                Float literals without trailing zero
            </a>
            <code-block lang="typescript">
            42.
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/053808f77b">
                Not Supported
            </a>
        </td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="scientific-notation">
                Float literals in scientific notation
            </a>
            <code-block lang="typescript">
            2e2
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/fbd87ab0b6">
                Not Supported
            </a>
        </td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="scientific-notation">
                Float literals in scientific notation
            </a>
            <code-block lang="typescript">
            2e2
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/fbd87ab0b6">
                Not Supported
            </a>
        </td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="literal-types.md" anchor="scientific-notation">
                Float hexadecimal literals in scientific notation
            </a>
            <code-block lang="typescript">
            0x42e2
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/4cf7bef40c">
                Not Supported
            </a>
        </td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/bc5bc798-7ef0-4afa-b3f6-813b52ba4a5f">
                Not Supported
            </a>
        </td>
    </tr>
</table>

## Shape Types

Below is a list of grammar of shaped types.


<table style="both">
    <tr>
        <td width="1"></td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan">PHPStan</a>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">11/11</format>
            </note>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">9/11</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">10/11</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="shape-types.md">
                Explicit shape types
            </a>
            <code-block lang="typescript">
            object {
                key: ValueType
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-4-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm does not support custom (class instances) objects" id="ref-4-1">
                    <code-block lang="typescript">
                    // OK
                    object {
                        key: ValueType
                    }
                    </code-block>
                    <code-block lang="typescript">
                    // Error
                    Custom\ObjectType {
                        key: ValueType
                    }
                    </code-block>
                    <a href="https://psalm.dev/r/4ec6feecc1">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="shape-types.md">
                Trailing comma in explicit shape types
            </a>
            <code-block lang="typescript">
            object {
                key: ValueType,
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-5-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm does not support custom (class instances) objects" id="ref-5-1">
                    <code-block lang="typescript">
                    // OK
                    object {
                        key: ValueType,
                    }
                    </code-block>
                    <code-block lang="typescript">
                    // Error
                    Custom\ObjectType {
                        key: ValueType,
                    }
                    </code-block>
                    <a href="https://psalm.dev/r/ba0ff971fe">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="shape-types.md">
                Implicit shape types
            </a>
            <code-block lang="typescript">
            object {
                ValueType
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-6-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm does not support custom (class instances) objects" id="ref-6-1">
                    <code-block lang="typescript">
                    // OK
                    object {
                        ValueType
                    }
                    </code-block>
                    <code-block lang="typescript">
                    // Error
                    Custom\ObjectType {
                        ValueType
                    }
                    </code-block>
                    <a href="https://psalm.dev/r/932713f109">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="shape-types.md">
                Trailing comma in implicit shape types
            </a>
            <code-block lang="typescript">
            object {
                ValueType,
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/0526cb9569">
                Not Supported
            </a>
        </td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="shape-types.md">
                Optional keys in explicit shape types
            </a>
            <code-block lang="typescript">
            object {
                key?: ValueType
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-7-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm does not support custom (class instances) objects" id="ref-7-1">
                    <code-block lang="typescript">
                    // OK
                    object {
                        key?: ValueType
                    }
                    </code-block>
                    <code-block lang="typescript">
                    // Error
                    Custom\ObjectType {
                        key?: ValueType
                    }
                    </code-block>
                    <a href="https://psalm.dev/r/d1f685e70d">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="shape-types.md">
                Empty (closed) shape types
            </a>
            <code-block lang="typescript">
            object {}
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-8-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm does not support custom (class instances) objects" id="ref-8-1">
                    <code-block lang="typescript">
                    // OK
                    object {}
                    </code-block>
                    <code-block lang="typescript">
                    // Error
                    Custom\ObjectType {}
                    </code-block>
                    <a href="https://psalm.dev/r/4581b137f2">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="shape-types.md" anchor="unsealed-shapes">
                Unsealed shape types
            </a>
            <code-block lang="typescript">
            object { ... }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-9-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm does not support custom (class instances) objects" id="ref-9-1">
                    <code-block lang="typescript">
                    // OK
                    object { ... }
                    </code-block>
                    <code-block lang="typescript">
                    // Error
                    Custom\ObjectType { ... }
                    </code-block>
                    <a href="https://psalm.dev/r/afad7f4c66">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="shape-types.md" anchor="unsealed-shapes">
                Explicit unsealed shape types
            </a>
            <code-block lang="typescript">
            object {
                key: ValueType,
                ...
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-10-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm does not support custom (class instances) objects" id="ref-10-1">
                    <code-block lang="typescript">
                    // OK
                    object {
                        key: ValueType,
                        ...
                    }
                    </code-block>
                    <code-block lang="typescript">
                    // Error
                    Custom\ObjectType {
                        key: ValueType,
                        ...
                    }
                    </code-block>
                    <a href="https://psalm.dev/r/65fdb88c08">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="shape-types.md" anchor="unsealed-shapes">
                Implicit unsealed shape types
            </a>
            <code-block lang="typescript">
            object {
                ValueType,
                ...
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-11-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm does not support custom (class instances) objects" id="ref-11-1">
                    <code-block lang="typescript">
                    // OK
                    object {
                        ValueType,
                        ...
                    }
                    </code-block>
                    <code-block lang="typescript">
                    // Error
                    Custom\ObjectType {
                        ValueType,
                        ...
                    }
                    </code-block>
                    <a href="https://psalm.dev/r/65fdb88c08">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="shape-types.md" anchor="typed-shapes">
                Typed unsealed shape types
            </a>
            <code-block lang="typescript">
            object {
                ...&lt;array-key, Type>
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-12-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="warning.svg"/> <a anchor="ref-12-2">Works with restrictions <sup>2</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm does not support custom (class instances) objects" id="ref-12-1">
                    <code-block lang="typescript">
                    // OK
                    object {
                        ...&lt;array-key, Type>
                    }
                    </code-block>
                    <code-block lang="typescript">
                    // Error
                    Custom\ObjectType {
                        ...&lt;array-key, Type>
                    }
                    </code-block>
                    <a href="https://psalm.dev/r/487c82502c">Open in psalm.dev</a>
                </def>
                <def title="2. PHPStan does not support some built-in types such as list (hardcoded in the parser)" id="ref-12-2">
                    <code-block lang="typescript">
                    // OK
                    array {
                        ...&lt;array-key, Type>
                    }
                    </code-block>
                    <code-block lang="typescript">
                    // Syntax Error
                    list {
                        array-key, 
                        ...&lt;Type>
                    }
                    </code-block>
                    <a href="https://phpstan.org/r/d89fdfaf-cee8-4737-b5fd-c9145458da6a">Open in phpstan.org</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="shape-types.md" anchor="attributes">
                Shape field attributes
            </a>
            <code-block lang="typescript">
            object {
                #[inline, assert(not&lt;"">)]
                name: string
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
    </tr>
</table>

## Callables Types

Below is a list of grammar of callable (function) types.

<table style="both">
    <tr>
        <td width="1"></td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan">PHPStan</a>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">15/15</format>
            </note>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">13/15</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">12/15</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md">
                Non-typed callable types
            </a>
            <code-block lang="typescript">
            callable()
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/a681d5d7-7632-42bf-bf8a-07ed1ae1c64c">
                Not Supported
            </a>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md">
                Typed callable types
            </a>
            <code-block lang="typescript">
            callable(): Type
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md">
                Callable with typed parameters
            </a>
            <code-block lang="typescript">
            callable(Type): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md" anchor="optional-parameters">
                Callable with optional parameters
            </a>
            <code-block lang="typescript">
            callable(Type=): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md" anchor="named-parameters">
                Callable with named parameters
            </a>
            <code-block lang="typescript">
            callable(Type $name): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md">
                Callable with optional named parameters
            </a>
            <code-block lang="typescript">
            callable(Type $name=): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/9ae58ed797">
                Not Supported
            </a>
        </td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md" anchor="output-parameters">
                Callable with output parameters
            </a>
            <code-block lang="typescript">
            callable(T&): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-13-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm does NOT support literal types" id="ref-13-1">
                    <code-block lang="typescript">
                    // OK
                    callable(T&): U
                    </code-block>
                    <code-block lang="typescript">
                    // Bug: Intersection types must be all objects,
                    // Psalm\Type\Atomic\TInt provided in docblock
                    callable(int&): U
                    </code-block>
                    <a href="https://psalm.dev/r/9a5a81443f">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md">
                Callable with output optional parameters
            </a>
            <code-block lang="typescript">
            callable(T&=): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-14-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm does NOT support literal types" id="ref-14-1">
                    <code-block lang="typescript">
                    // OK
                    callable(T&=): U
                    </code-block>
                    <code-block lang="typescript">
                    // Bug: Intersection types must be all objects,
                    // Psalm\Type\Atomic\TInt provided in docblock
                    callable(int&=): U
                    </code-block>
                    <a href="https://psalm.dev/r/33dde88598">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md">
                Callable with output optional named parameters
            </a>
            <code-block lang="typescript">
            callable(T &$name=): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://psalm.dev/r/7d7c34e559">
                Not Supported
            </a>
        </td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md" anchor="variadic-parameters">
                Callable with suffixed variadic parameters
            </a>
            <code-block lang="typescript">
            callable(Type...): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md" anchor="variadic-parameters">
                Callable with prefixed variadic parameters
            </a>
            <code-block lang="typescript">
            callable(...Type): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/a578af59-2099-4244-a297-67b74f9729b5">
                Not Supported
            </a>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md" anchor="variadic-parameters">
                Callable with prefixed and suffixed variadic parameters
            </a>
            <code-block lang="typescript">
            callable(...Type ...$name): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-15-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="warning.svg"/> <a anchor="ref-15-2">Works with restrictions <sup>2</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <tip>
                This expression is incorrect because the "variadic" 
                lexeme <code>...</code> must be present in the parameter 
                in a single copy. The parser must throw a syntax or 
                semantic error.
            </tip>
            <deflist collapsible="true">
                <def title="1. Psalm throws an internal error instead of a valid error message" id="ref-15-1">
                    <code-block lang="typescript">
                    callable(...int ...$name)
                    // Internal Psalm error on line ...: 
                    // Unrecognised parse tree type Psalm\Internal\Type\ParseTree\CallableParamTree
                    </code-block>
                    <a href="https://psalm.dev/r/d802f62027">Open in psalm.dev</a>
                </def>
                <def title="2. PHPStan does not support prefixed variadic parameters" id="ref-15-2">
                    <code-block lang="typescript">
                    callable(...int ...$name)
                    // PHPDoc tag @return has invalid value (callable(...int ...$name): T): 
                    // Unexpected token "(", expected TOKEN_HORIZONTAL_WS at ...
                    </code-block>
                    <a href="https://phpstan.org/r/f1f73f78-cebe-4cf6-887c-e6516d684230">Open in phpstan.org</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md" anchor="variadic-parameters">
                Callable with optional variadic parameters
            </a>
            <code-block lang="typescript">
            callable(Type...=): T
            callable(...Type=): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-16-1">Works with restrictions <sup>1</sup></a></td>
        <td>
            <icon src="ko.svg" />
            <a href="https://phpstan.org/r/0a7c61e9-691d-414c-983c-b5c32f472214">
                No Error
            </a>
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <tip>
                This expression is incorrect because any variadic 
                parameter is already optional. The parser must 
                throw a syntax or semantic error.
            </tip>
            <deflist collapsible="true">
                <def title="1. Psalm error depends on parameter definition syntax" id="ref-16-1">
                    <code-block lang="typescript">
                    // OK: Cannot have variadic param with a default in docblock
                    callable(...T=): T
                    </code-block>
                    <code-block lang="typescript">
                    // Bug: Cannot have duplicate tokens in docblock
                    callable(T...=): T
                    </code-block>
                    <a href="https://psalm.dev/r/7e5482be69">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md" anchor="variadic-parameters">
                Callable with named variadic parameters
            </a>
            <code-block lang="typescript">
            callable(Type ...$name): T
            callable(...Type $name): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-17-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="warning.svg"/> <a anchor="ref-17-2">Works with restrictions <sup>2</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm throws an internal error if using prefix syntax" id="ref-17-1">
                    <code-block lang="typescript">
                    callable(...Type $name): T
                    // Internal Psalm error on line ...: 
                    // Unrecognised parse tree type Psalm\Internal\Type\ParseTree\CallableParamTree
                    </code-block>
                    <a href="https://psalm.dev/r/2cf0778b29">Open in psalm.dev</a>
                </def>
                <def title="2. PHPStan does not support prefixed variadic parameters" id="ref-17-2">
                   <code-block lang="typescript">
                    callable(...Type $name): T
                    // PHPDoc tag @return has invalid value (callable(...Type $name): T): 
                    // Unexpected token "(", expected TOKEN_HORIZONTAL_WS at ...
                    </code-block>
                    <a href="https://phpstan.org/r/13de8276-eeac-4a0f-8344-06025ca3c781">Open in phpstan.org</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <a href="callable-types.md" anchor="variadic-parameters">
                Callable with output named variadic parameters
            </a>
            <code-block lang="typescript">
            callable(Type &...$name): T
            callable(...Type &$name): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-18-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="warning.svg"/> <a anchor="ref-18-2">Works with restrictions <sup>2</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">
            <deflist collapsible="true">
                <def title="1. Psalm does NOT support literal types" id="ref-18-1">
                    <code-block lang="typescript">
                    // OK
                    callable(T &...$name): U
                    </code-block>
                    <code-block lang="typescript">
                    // Bug: Intersection types must be all objects,
                    // Psalm\Type\Atomic\TInt provided in docblock
                    callable(int &...$name): U
                    </code-block>
                    <a href="https://psalm.dev/r/33dde88598">Open in psalm.dev</a>
                </def>
                <def title="2. PHPStan does not support prefixed variadic parameters" id="ref-18-2">
                   <code-block lang="typescript">
                    callable(...Type &$name): T
                    // PHPDoc tag @return has invalid value (callable(...Type &$name): T): 
                    // Unexpected token "(", expected TOKEN_HORIZONTAL_WS at ...
                    </code-block>
                    <a href="https://phpstan.org/r/c5eba3e1-8c59-4066-a96a-070b82632495">Open in phpstan.org</a>
                </def>
            </deflist>
        </td>
    </tr>
</table>
