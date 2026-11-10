# Syntax Comparison

<show-structure for="chapter" depth="2"/>

The language is defined by a syntax that is based on the grammar of popular
static code analysis tools: [PHPStan](https://phpstan.org/) and [Psalm](https://psalm.dev/).

- PHPStan: [https://phpstan.org](https://phpstan.org/writing-php-code/phpdoc-types)
- Psalm: [https://psalm.dev](https://psalm.dev/docs/annotating_code/type_syntax/atomic_types/)

> Below is a list of the syntactic structures (grammar) of the three parsers:
> the basic ones they all read the same way, and those on which they differ or
> one of them has a bug. The logical component (the physical existence of the
> type) is **NOT** taken into account.
> {style="note"}

Only the plain names such as `int` or `string` are left out, every other
construct is listed.

<tip>
<icon src="ok.svg"/> the parser read the source as written.
<icon src="ko.svg"/> the parser rejected the source.
<icon src="warning.svg"/> the parser accepted the source but changed its meaning
(a word was dropped, words were glued together, a value was clamped).
In the <a anchor="malformed-input">Malformed input</a> section the meaning is
reversed: <icon src="ok.svg"/> is a rejection, <icon src="warning.svg"/> is an
acceptance of garbage.
</tip>

General table across all type parsing capabilities

<table style="header-row">
    <tr>
        <td></td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan/phpdoc-parser">PHPStan</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
    </tr>
    <tr>
        <td>Constructs read as written</td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">177/241</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">107/241</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">238/241</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td>Malformed inputs rejected</td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">29/34</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">5/34</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">33/34</format>
            </warning>
        </td>
    </tr>
</table>

> The counts are taken over the rows of this page only, that is over the basic
> constructs and the constructs on which the parsers disagree. They are not
> the share of the whole language each parser reads.
> {style="note"}

## Methodology

Each verdict below is the answer of the tool's own parser, asked directly, so
the table says what the tools do rather than what they are said to do.

<table style="header-row">
    <tr>
        <td>Tool</td>
        <td>Version</td>
        <td>A type is supported when</td>
    </tr>
    <tr>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;PHPStan
        </td>
        <td><code>phpstan/phpdoc-parser</code> 2.4</td>
        <td><code>TypeParser::parse()</code> reads the source whole, leaving no trailing input</td>
    </tr>
    <tr>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;Psalm
        </td>
        <td><code>vimeo/psalm</code> 7.0</td>
        <td><code>Psalm\Type::parseString()</code> returns a type carrying what was written</td>
    </tr>
    <tr>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;TypeLang
        </td>
        <td><code>type-lang/parser</code> 2.0</td>
        <td><code>TypeParser::parse()</code> returns a node, every feature of the parser enabled</td>
    </tr>
</table>

The tools are run with their default settings. Psalm is given the templates
`T U V K K2 L` and the parameters `$a $b`, otherwise it cannot read `T[K]` and
conditional types at all. The comparison covers the parsing of a string only:
error messages, offsets and the error-tolerant mode have no counterpart in the
other tools.

> Psalm answers even where it did not understand the question. It reads a word
> it cannot parse as a name, so `int string` comes back as a type called
> `intstring`, and it cuts off what stands behind the part it understood.
> A verdict here counts neither: such an answer is <icon src="warning.svg"/>.
> {style="note"}

## Names

Class names, pseudo types and `$this`.

<table style="both">
    <tr>
        <td></td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan/phpdoc-parser">PHPStan</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
    </tr>
    <tr>
        <td>supported</td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">8/8</format>
            </note>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">5/8</format>
            </warning>
        </td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">8/8</format>
            </note>
        </td>
    </tr>
    <tr>
        <td>
            <code>\Foo\Bar</code>
            <br/>Fully qualified name
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>non-empty-string</code>
            <br/>Pseudo type with hyphens
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array-key</code>
            <br/>Pseudo type with a hyphen
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>positive-int</code>
            <br/>Integer refinement
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>namespace\Foo</code>
            <br/>Relative name
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo-bar</code>
            <br/>Hyphen inside a name. PHPStan allows it anywhere, Psalm only for known pseudo types
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>$this</code>
            <br/>Psalm resolves it to `static`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>?$this</code>
            <br/>Psalm resolves it to `static`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

## Literals

Boolean, null, integer, float and string literals.

<table style="both">
    <tr>
        <td></td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan/phpdoc-parser">PHPStan</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
    </tr>
    <tr>
        <td>supported</td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">29/30</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">11/30</format>
            </warning>
        </td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">30/30</format>
            </note>
        </td>
    </tr>
    <tr>
        <td>
            <code>true|false</code>
            <br/>Union of boolean literals
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>42</code>
            <br/>Integer literal
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>-42</code>
            <br/>Negative integer literal
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>1.5</code>
            <br/>Float literal
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>-1.5</code>
            <br/>Negative float literal
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>'a'</code>
            <br/>Single-quoted string literal
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>"a"</code>
            <br/>Double-quoted string literal
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>1|2|3</code>
            <br/>Union of integer literals
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>'a'|'b'</code>
            <br/>Union of string literals
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>true</code>
            <br/>Boolean literal
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>null</code>
            <br/>Null literal
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>+1</code>
            <br/>Explicit plus sign
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>- 1</code>
            <br/>Whitespace after the sign. PHPStan rejects it, Psalm reads it with distortion
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>007</code>
            <br/>Legacy octal
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>0x1F</code>
            <br/>Hexadecimal
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>-0x1F</code>
            <br/>Signed hexadecimal
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>0b101</code>
            <br/>Binary
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>0o17</code>
            <br/>Explicit octal
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>1e3</code>
            <br/>Exponent without a fraction
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>1_000.5</code>
            <br/>Separator in a float
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>.5</code>
            <br/>Float without the integer part
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>1.</code>
            <br/>Float without the fraction part
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>1.5e3</code>
            <br/>Float with an exponent
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>9223372036854775808</code>
            <br/>Above `PHP_INT_MAX`. Psalm clamps it to the maximum
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>-9223372036854775809</code>
            <br/>Below `PHP_INT_MIN`. Psalm clamps it to the minimum
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>'a"b'</code>
            <br/>Double quote inside single quotes. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>"a\"b"</code>
            <br/>Escaped quote. Psalm keeps the backslash in the value
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>"a\nb"</code>
            <br/>Escape sequence. Psalm does not decode it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>"a\x41"</code>
            <br/>Hexadecimal escape. Psalm does not decode it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>"a\u{1F600}"</code>
            <br/>Unicode escape. Psalm does not decode it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

## Constants and masks

Global constants, class constants and masks (`*` as a wildcard in a constant name).

<table style="both">
    <tr>
        <td></td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan/phpdoc-parser">PHPStan</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
    </tr>
    <tr>
        <td>supported</td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">9/17</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">12/17</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">15/17</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td>
            <code>PHP_EOL</code>
            <br/>Global constant
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>\PHP_EOL</code>
            <br/>Fully qualified global constant
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo::BAR</code>
            <br/>Class constant
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo\Bar::BAZ</code>
            <br/>Class constant of a qualified class
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo::class</code>
            <br/>Class name constant
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo::BAR|Foo::BAZ</code>
            <br/>Union of class constants
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo::*</code>
            <br/>Every constant of a class
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>PHP_*</code>
            <br/>Global constant mask. PHPStan stops at the star
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Some\Any\CONST_*</code>
            <br/>Mask in a namespace
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>FOO*BAR</code>
            <br/>Star in the middle of a name
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>*_SOME</code>
            <br/>Star at the start of a name
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Some\Any\*</code>
            <br/>Every constant of a namespace
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>\*</code>
            <br/>Every constant of the root namespace
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo::*_BAZ</code>
            <br/>Class constant mask with a leading star. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo::B*R</code>
            <br/>Class constant mask with a star in the middle. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>$this::BAR</code>
            <br/>Constant of a variable. Only Psalm reads it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>$this::class</code>
            <br/>Class of a variable. Only Psalm reads it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
    </tr>
</table>

## Lists and offsets

List `T[]` and offset access `T[K]`.

<table style="both">
    <tr>
        <td></td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan/phpdoc-parser">PHPStan</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
    </tr>
    <tr>
        <td>supported</td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">19/21</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">9/21</format>
            </warning>
        </td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">21/21</format>
            </note>
        </td>
    </tr>
    <tr>
        <td>
            <code>int[]</code>
            <br/>List of a type
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int[][]</code>
            <br/>Nested list
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(A|B)[]</code>
            <br/>List of a group
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array&lt;int&gt;[]</code>
            <br/>List of a generic
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>?int[]</code>
            <br/>Nullable list
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int[]|null</code>
            <br/>List or `null`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo::BAR[]</code>
            <br/>List of a class constant
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>T[K]</code>
            <br/>Offset access
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>T[int]</code>
            <br/>Arbitrary type as an index. Psalm accepts only declared templates
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>T['a']</code>
            <br/>Literal as an index
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>T[Foo::A]</code>
            <br/>Class constant as an index
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>T[K|L]</code>
            <br/>Union as an index
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>T[?K]</code>
            <br/>Nullable index
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(A|B)[K]</code>
            <br/>Offset access on a group
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(?T)[K]</code>
            <br/>Offset access on a nullable group
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo&lt;int&gt;[K]</code>
            <br/>Offset access on a generic
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>list&lt;int&gt;[0]</code>
            <br/>Offset access on a list
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{a: int}['a']</code>
            <br/>Offset access on a shape
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>T [K]</code>
            <br/>Space before the brackets. PHPStan rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int\n[]</code>
            <br/>Line break before `[]`. Psalm glues it into the name
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>$this[]</code>
            <br/>Psalm resolves `$this` to `static`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

## Logical types

Union `|`, intersection `&` and nullable `?`, and how they bind next to each other.

<table style="both">
    <tr>
        <td></td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan/phpdoc-parser">PHPStan</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
    </tr>
    <tr>
        <td>supported</td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">14/25</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">14/25</format>
            </warning>
        </td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">25/25</format>
            </note>
        </td>
    </tr>
    <tr>
        <td>
            <code>A|B</code>
            <br/>Union
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A|B|C</code>
            <br/>Union of three
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A&amp;B</code>
            <br/>Intersection
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A&amp;B&amp;C</code>
            <br/>Intersection of three
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(A|B)&amp;C</code>
            <br/>Union inside an intersection
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(A&amp;B)|C</code>
            <br/>Intersection inside a union
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>?int</code>
            <br/>Nullable type
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A&amp;B|C</code>
            <br/>Mixed operators without parentheses. PHPStan rejects them
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A|B&amp;C</code>
            <br/>Mixed operators without parentheses. PHPStan rejects them
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A&amp;B|C&amp;D</code>
            <br/>Two intersections in a union
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>?A|B</code>
            <br/>`?` before a union member. PHPStan rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>?T|U</code>
            <br/>`?` before a union member of templates. PHPStan rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>?T|U[]</code>
            <br/>`?` before a union member and a list. PHPStan rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A|?B</code>
            <br/>`?` inside a union. PHPStan rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>?A&amp;B</code>
            <br/>Psalm reads `null|(A&amp;B)`, the mark binds weaker there
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A&amp;?B</code>
            <br/>Psalm silently drops the `null`
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>?A&amp;?B</code>
            <br/>Psalm loses `B`
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(?A)&amp;B</code>
            <br/>The only form PHPStan accepts. Psalm drops the `null`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(A&amp;B)&amp;C</code>
            <br/>Nested intersection. Psalm loses `B`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(int|string)&amp;bool</code>
            <br/>Union inside an intersection. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A[]|B[]</code>
            <br/>Psalm merges the arrays into one
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A[]&amp;B[]</code>
            <br/>Psalm merges the arrays into one
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A&amp;B[]</code>
            <br/>Suffix binds tighter than `&amp;`. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A|B[K]</code>
            <br/>Offset access in a union. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(A|?B)[]</code>
            <br/>Nullable inside a grouped list. Psalm loses `B`
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

## Generics

Arguments of `Name<...>`.

<table style="both">
    <tr>
        <td></td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan/phpdoc-parser">PHPStan</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
    </tr>
    <tr>
        <td>supported</td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">12/14</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">8/14</format>
            </warning>
        </td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">14/14</format>
            </note>
        </td>
    </tr>
    <tr>
        <td>
            <code>Foo\Bar&lt;int&gt;</code>
            <br/>Qualified name with an argument
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array&lt;int, string&gt;</code>
            <br/>Generic with two arguments
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo&lt;int&gt;</code>
            <br/>Generic on any class name
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>iterable&lt;int&gt;</code>
            <br/>Generic `iterable`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>list&lt;int&gt;</code>
            <br/>Generic list
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>non-empty-list&lt;int&gt;</code>
            <br/>Non-empty generic list
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo&lt;int, string,&gt;</code>
            <br/>Trailing comma
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo&lt;*&gt;</code>
            <br/>Wildcard argument
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo&lt;int, *&gt;</code>
            <br/>Wildcard after another argument
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo&lt;covariant T&gt;</code>
            <br/>Variance. Psalm drops the keyword silently
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo&lt;contravariant T&gt;</code>
            <br/>Variance. Psalm drops the keyword silently
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo&lt;invariant T&gt;</code>
            <br/>Explicit invariance. Only TypeLang
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo&lt;int&gt;[K]</code>
            <br/>Offset access over a generic
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo&lt;T is int ? A : B&gt;</code>
            <br/>Conditional type as an argument without parentheses
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

## Templates

Template parameters of a callable: `callable<T of X = Y>(T): T`.

<table style="both">
    <tr>
        <td></td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan/phpdoc-parser">PHPStan</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
    </tr>
    <tr>
        <td>supported</td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">11/12</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">0/12</format>
            </warning>
        </td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">12/12</format>
            </note>
        </td>
    </tr>
    <tr>
        <td>
            <code>callable&lt;T&gt;(T): T</code>
            <br/>Template parameter
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable&lt;T, U,&gt;(T): U</code>
            <br/>Several parameters with a trailing comma
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable&lt;T of int&gt;(T): T</code>
            <br/>Upper bound `of`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable&lt;T as int&gt;(T): T</code>
            <br/>Upper bound `as`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable&lt;T super int&gt;(T): T</code>
            <br/>Lower bound `super`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable&lt;T of A super B&gt;(): void</code>
            <br/>Both bounds
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable&lt;T = int&gt;(T): T</code>
            <br/>Default type
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable&lt;T of int = int&gt;(T): T</code>
            <br/>Bound and default
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable&lt;covariant T&gt;(T): T</code>
            <br/>Variance of a template. Only TypeLang
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Closure&lt;T&gt;(T): T</code>
            <br/>`Closure` with a template
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>pure-Closure&lt;T&gt;(T): T</code>
            <br/>`pure-Closure` with a template
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo&lt;T&gt;(T): T</code>
            <br/>Any name before the parentheses
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

## Callable

Parameters, return types and modifiers of `callable(...)` and `Closure(...)`.

<table style="both">
    <tr>
        <td></td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan/phpdoc-parser">PHPStan</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
    </tr>
    <tr>
        <td>supported</td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">17/31</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">13/31</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">30/31</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td>
            <code>callable(): void</code>
            <br/>Callable without parameters
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int): void</code>
            <br/>Callable with a parameter
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int $a): void</code>
            <br/>Callable with a named parameter
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int ...$a): void</code>
            <br/>Callable with a variadic parameter
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Closure(int): void</code>
            <br/>`Closure` with a signature
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>pure-callable(int): void</code>
            <br/>Pure callable
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int)</code>
            <br/>No return type. PHPStan requires it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(...int): void</code>
            <br/>Variadic marker before the type
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int... $a): void</code>
            <br/>Variadic marker right after the type. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(...): void</code>
            <br/>Variadic mixed parameter. TypeLang rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(...$a): void</code>
            <br/>Variadic parameter without a type
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable($a): void</code>
            <br/>Parameter without a type
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int &amp;$a): void</code>
            <br/>By-reference parameter
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(&amp;$a): void</code>
            <br/>By-reference parameter without a type
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int&amp;): void</code>
            <br/>By-reference marker without a name. Psalm drops the `&amp;`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int &amp;...$a): void</code>
            <br/>By-reference variadic. Psalm drops the `&amp;`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int $a=): void</code>
            <br/>Optional parameter
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int=)</code>
            <br/>Optional parameter without a name
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int = 42): void</code>
            <br/>Default value without a name. Psalm skips it and loses the value
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int $a = 42): void</code>
            <br/>Default value. Neither PHPStan nor Psalm parses it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int $a = -1): void</code>
            <br/>Signed default value
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int $a = PHP_INT_MAX): void</code>
            <br/>Constant as a default value
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int $a = Foo::BAR): void</code>
            <br/>Class constant as a default value
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(): $this</code>
            <br/>Return `$this`. Psalm resolves it to `static`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(): T is int ? A : B</code>
            <br/>Conditional return type without parentheses
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>foo(T): void</code>
            <br/>Any name before the parentheses
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable (int): void</code>
            <br/>Space before the parentheses
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(): T|U</code>
            <br/>Union after the return type. All three read `(callable(): T)|U`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(): T&amp;U</code>
            <br/>Intersection after the return type. Psalm folds it into the return type
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(): ?T|U</code>
            <br/>Nullable member of a union after the return type. Psalm folds it into the return type
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(): callable(): T|U</code>
            <br/>Nested callable with a union. PHPStan rejects it, Psalm folds the union
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

## Shapes

Fields of `array{}`, `list{}` and `object{}`.

<table style="both">
    <tr>
        <td></td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan/phpdoc-parser">PHPStan</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
    </tr>
    <tr>
        <td>supported</td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">23/28</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">13/28</format>
            </warning>
        </td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">28/28</format>
            </note>
        </td>
    </tr>
    <tr>
        <td>
            <code>array{}</code>
            <br/>Empty shape
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{a: int}</code>
            <br/>Shape with a named field
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{a?: int}</code>
            <br/>Optional field
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{int, string}</code>
            <br/>Shape with positional fields
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{0: int}</code>
            <br/>Integer key
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{'a': int}</code>
            <br/>String key
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{a: array{b: int}}</code>
            <br/>Nested shape
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{a: int, ...}</code>
            <br/>Unsealed shape
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>list{int, string}</code>
            <br/>List shape
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>object{a: int}</code>
            <br/>Object shape
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{int, string,}</code>
            <br/>Trailing comma
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{a: int, string}</code>
            <br/>Named and positional fields together
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{Foo::BAR: int}</code>
            <br/>Class constant as a key
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{covariant: int}</code>
            <br/>Keyword as a key. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{'Foo\Bar': int}</code>
            <br/>Backslash in a string key. Psalm drops it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{- 1: int}</code>
            <br/>Whitespace after the sign in a key. PHPStan rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{...}</code>
            <br/>Unsealed shape. Psalm reads an empty array
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{a: int, ...&lt;string&gt;}</code>
            <br/>Unsealed shape with a value type
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{a: int, ...&lt;string, int&gt;}</code>
            <br/>Unsealed shape with key and value types
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>list{1: int}</code>
            <br/>Key in a list. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>list{...}</code>
            <br/>Unsealed list. Psalm loses the tail
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>non-empty-array{a: int}</code>
            <br/>Shape of a non-empty array
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>object{a: int,}</code>
            <br/>Trailing comma in an object shape
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>object{a: int, ...}</code>
            <br/>Unsealed object. Psalm drops `...`
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>object{0: int}</code>
            <br/>Numeric property. PHPStan rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>object{Foo::BAR: int}</code>
            <br/>Class constant as a property
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo{a: int}</code>
            <br/>Shape on any class name
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{a: int} | array{b: string}</code>
            <br/>Union of shapes. Psalm merges them into one
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

## Conditional types

The `T is U ? A : B` family.

<table style="both">
    <tr>
        <td></td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan/phpdoc-parser">PHPStan</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
    </tr>
    <tr>
        <td>supported</td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">11/18</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">5/18</format>
            </warning>
        </td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">18/18</format>
            </note>
        </td>
    </tr>
    <tr>
        <td>
            <code>(T is int ? A : B)</code>
            <br/>Conditional type in parentheses
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(T is not int ? A : B)</code>
            <br/>Negated conditional type in parentheses
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>($a is int ? A : B)</code>
            <br/>Conditional type on a parameter in parentheses
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>T is int ? A : B</code>
            <br/>Without parentheses. PHPStan and Psalm require them
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>T is not int ? A : B</code>
            <br/>Negation without parentheses
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>$a is int ? A : B</code>
            <br/>Parameter as a subject without parentheses
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(T is ?int ? A : B)</code>
            <br/>Nullable type in the condition. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(T is int[] ? A : B)</code>
            <br/>List type in the condition. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(T is callable(int): void ? A : B)</code>
            <br/>Callable in the condition. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(T is int ? A : B is C ? D : E)</code>
            <br/>Nested condition in the else branch. Only PHPStan and TypeLang
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(T is int ? A : (B is C ? D : E))</code>
            <br/>Nested condition in parentheses. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(T is $this ? A : B)</code>
            <br/>Psalm resolves `$this` to `static`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>($this is int ? A : B)</code>
            <br/>Subject `$this`. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int &amp;$a): ($a is int ? A : B)</code>
            <br/>Subject is a by-reference parameter. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{a: T is int ? A : B}</code>
            <br/>Inside a shape without parentheses. PHPStan rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(T $a): T is int ? A : B</code>
            <br/>As a return type without parentheses. PHPStan rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>?T is U ? V : Z</code>
            <br/>Nullable subject. Psalm moves `null` out of the condition, PHPStan rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A|B is C ? D : E</code>
            <br/>Union as a subject. TypeLang binds the condition weaker than the union, the others reject it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

## Special names

Names that carry meaning in PHPStan and Psalm. PHPStan and TypeLang read them as ordinary `Name<arguments>` and leave the meaning to the next layer, Psalm checks them while parsing.

<table style="both">
    <tr>
        <td></td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan/phpdoc-parser">PHPStan</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
    </tr>
    <tr>
        <td>supported</td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">16/16</format>
            </note>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">6/16</format>
            </warning>
        </td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">16/16</format>
            </note>
        </td>
    </tr>
    <tr>
        <td>
            <code>int&lt;0, max&gt;</code>
            <br/>Integer range
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int&lt;min, 0&gt;</code>
            <br/>Integer range with `min`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>class-string&lt;Foo&gt;</code>
            <br/>Class string
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>key-of&lt;T&gt;</code>
            <br/>Key of a template
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>value-of&lt;T&gt;</code>
            <br/>Value of a template
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int-mask&lt;1, 2, 4&gt;</code>
            <br/>Mask of integers
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int&lt;10, 0&gt;</code>
            <br/>Reversed range
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int&lt;PHP_INT_MIN, PHP_INT_MAX&gt;</code>
            <br/>Constants as the bounds
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int-mask&lt;1, 2, 4,&gt;</code>
            <br/>Trailing comma in a mask
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int-mask-of&lt;1|2|4&gt;</code>
            <br/>Mask of a union
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>class-string&lt;int&gt;</code>
            <br/>Class string of a scalar
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>enum-string&lt;Foo&gt;</code>
            <br/>Psalm turns it into `class-string`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>trait-string&lt;Foo&gt;</code>
            <br/>Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>key-of&lt;T, U&gt;</code>
            <br/>Psalm drops the second argument
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>uppercase-string</code>
            <br/>Psalm drops the refinement
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>open-resource</code>
            <br/>Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

## Whitespace and comments

What may stand between tokens. Newlines are written as `\n`.

<table style="both">
    <tr>
        <td></td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan/phpdoc-parser">PHPStan</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
    </tr>
    <tr>
        <td>supported</td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">8/21</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">11/21</format>
            </warning>
        </td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">21/21</format>
            </note>
        </td>
    </tr>
    <tr>
        <td>
            <code>\nint</code>
            <br/>Leading line break
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int |\n string</code>
            <br/>Line break after an operator
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array&lt;\n    int,\n    string\n&gt;</code>
            <br/>Multiline generic. Psalm rejects it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int\n$a): void</code>
            <br/>Line break inside a parameter
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array {a: int}</code>
            <br/>Space before the brace of a shape. PHPStan rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int []</code>
            <br/>Space before `[]`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int [ ]</code>
            <br/>Spaces inside `[]`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>T [K]</code>
            <br/>Space before an offset. PHPStan rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo &lt;int&gt;</code>
            <br/>Space before the angle bracket
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array &lt;K, V&gt;</code>
            <br/>Space before the angle bracket of `array`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo :: BAR</code>
            <br/>Spaces around `::`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>? int</code>
            <br/>Space after `?`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int | string</code>
            <br/>Spaces around `|`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable( int )</code>
            <br/>Spaces inside the parentheses. PHPStan rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable(int , string)</code>
            <br/>Space before a comma. PHPStan rejects it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>callable&lt;T&gt; (T)</code>
            <br/>Space between a template list and the parameters. PHPStan and Psalm reject it
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int /* c */</code>
            <br/>Block comment
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int // c</code>
            <br/>Line comment
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int # c</code>
            <br/>Hash comment
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{a: int /* c */, b: string}</code>
            <br/>Comment inside a shape
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Some&lt;/* a */int/* b */,/* c */string&gt;</code>
            <br/>Comments inside generics
        </td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

## Malformed input

Inputs that must be rejected. A parser that accepts them reads garbage as a type.

<table style="both">
    <tr>
        <td></td>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;<a href="https://github.com/phpstan/phpdoc-parser">PHPStan</a>
        </td>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;<a href="https://github.com/vimeo/psalm">Psalm</a>
        </td>
        <td>
            <icon src="typelang.svg" height="20"/>&nbsp;<a href="https://github.com/php-type-language">TypeLang</a>
        </td>
    </tr>
    <tr>
        <td>rejected</td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">29/34</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">5/34</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">33/34</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td>
            <code>int string</code>
            <br/>Two words. Psalm glues them into `intstring`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo&lt;A B&gt;</code>
            <br/>Two arguments without a comma. Psalm glues them
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{a: int b: string}</code>
            <br/>Two fields without a comma. Psalm glues the words
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int)</code>
            <br/>Stray bracket. Psalm ignores it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int|</code>
            <br/>Dangling `|`. Psalm drops it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A&amp;</code>
            <br/>Dangling `&amp;`. Psalm drops it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A&amp;&amp;B</code>
            <br/>Doubled `&amp;`. Psalm reads `A&amp;B`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(A|)</code>
            <br/>Dangling `|` in a group. Psalm drops it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>??int</code>
            <br/>Doubled `?`. Psalm folds it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int?[]</code>
            <br/>Psalm drops the mark and the brackets
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int ? a</code>
            <br/>Psalm ignores the tail
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>int *</code>
            <br/>Psalm accepts a trailing `*`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>PHP_ *</code>
            <br/>Space inside a mask. Psalm glues it into `PHP_*`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>in t</code>
            <br/>Psalm reads `int`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>'a</code>
            <br/>Unterminated string. Psalm accepts and truncates it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>'a''b'</code>
            <br/>Two strings in a row. Psalm keeps the first
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo\</code>
            <br/>Trailing backslash. Psalm accepts and ignores it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo\\Bar</code>
            <br/>Doubled backslash
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>foo-</code>
            <br/>Trailing hyphen in a name. PHPStan accepts it
        </td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>08</code>
            <br/>Invalid octal. PHPStan accepts it
        </td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>1__000</code>
            <br/>Doubled underscore. Psalm accepts it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>1._5</code>
            <br/>Underscore after the dot. PHPStan accepts it
        </td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo::BAR::class</code>
            <br/>Psalm drops the second constant
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(Foo)::class</code>
            <br/>Group before `::`. Psalm drops the constant
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo::BAR-BAZ</code>
            <br/>Hyphen in a constant name. PHPStan accepts it
        </td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo&lt;T of Bar&gt;</code>
            <br/>Bound in a generic argument. Psalm reads a template map
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>Foo&lt;out T&gt;</code>
            <br/>Psalm glues the words into `outT`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{a: int, ...int}</code>
            <br/>Unsealed tail without brackets. Psalm ignores it
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{$a: int}</code>
            <br/>Variable as a key. Psalm reads a string key
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{PHP_*: int}</code>
            <br/>Mask as a key. Psalm reads a string key
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>array{a: int, a: string}</code>
            <br/>Duplicate key. Psalm rejects it, PHPStan and TypeLang keep both
        </td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>(T is not not int ? A : B)</code>
            <br/>Doubled `not`. Psalm glues the words
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>T is int ? A</code>
            <br/>Condition without the else branch. Psalm drops the condition
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td>
            <code>A is B ? C</code>
            <br/>Condition without the else branch. Psalm reads `A`
        </td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

