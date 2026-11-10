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
                <format style="bold" color="RosyBrown">180/239</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">108/239</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">236/239</format>
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
        <td><code>\Foo\Bar</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>non-empty-string</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array-key</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>positive-int</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>namespace\Foo</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo-bar</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-names-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>$this</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-names-2"><icon src="warning.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?$this</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-names-2"><icon src="warning.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

<list type="decimal">
    <li id="fn-names-1">PHPStan allows it anywhere, Psalm only for known pseudo types.</li>
    <li id="fn-names-2">Psalm resolves it to `static`.</li>
</list>

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
        <td><code>true|false</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>42</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>-42</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>1.5</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>-1.5</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>'a'</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>"a"</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>1|2|3</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>'a'|'b'</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>true</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>null</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>+1</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>- 1</code></td>
        <td><a anchor="fn-literals-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><a anchor="fn-literals-3"><icon src="warning.svg"/><sup>3</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>007</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>0x1F</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-5"><icon src="ko.svg"/><sup>5</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>-0x1F</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-5"><icon src="ko.svg"/><sup>5</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>0b101</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-6"><icon src="ko.svg"/><sup>6</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>0o17</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-7"><icon src="ko.svg"/><sup>7</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>1e3</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-8"><icon src="ko.svg"/><sup>8</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>1_000.5</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-9"><icon src="ko.svg"/><sup>9</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>.5</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-10"><icon src="ko.svg"/><sup>10</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>1.</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-10"><icon src="ko.svg"/><sup>10</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>1.5e3</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-8"><icon src="ko.svg"/><sup>8</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>9223372036854775808</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-11"><icon src="warning.svg"/><sup>11</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>-9223372036854775809</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-12"><icon src="warning.svg"/><sup>12</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>'a"b'</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-13"><icon src="ko.svg"/><sup>13</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>"a\"b"</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-14"><icon src="warning.svg"/><sup>14</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>"a\nb"</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-15"><icon src="warning.svg"/><sup>15</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>"a\x41"</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-15"><icon src="warning.svg"/><sup>15</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>"a\u{1F600}"</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-literals-15"><icon src="warning.svg"/><sup>15</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

<list type="decimal">
    <li id="fn-literals-1">Psalm has no explicit plus sign in numbers.</li>
    <li id="fn-literals-2">PHPStan does not allow a space after the sign of a number.</li>
    <li id="fn-literals-3">Psalm reads it as `-1`.</li>
    <li id="fn-literals-4">Psalm has no legacy octal numbers.</li>
    <li id="fn-literals-5">Psalm has no hexadecimal numbers.</li>
    <li id="fn-literals-6">Psalm has no binary numbers.</li>
    <li id="fn-literals-7">Psalm has no `0o` octal prefix.</li>
    <li id="fn-literals-8">Psalm has no exponent in floats.</li>
    <li id="fn-literals-9">Psalm has no digit separators in numbers.</li>
    <li id="fn-literals-10">Psalm requires digits on both sides of the decimal point.</li>
    <li id="fn-literals-11">Psalm clamps it to the maximum.</li>
    <li id="fn-literals-12">Psalm clamps it to the minimum.</li>
    <li id="fn-literals-13">Psalm does not allow a double quote inside a single-quoted string.</li>
    <li id="fn-literals-14">Psalm keeps the backslash in the value.</li>
    <li id="fn-literals-15">Psalm does not decode it.</li>
</list>

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
        <td><code>PHP_EOL</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>\PHP_EOL</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo::BAR</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo\Bar::BAZ</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo::class</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo::BAR|Foo::BAZ</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo::*</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>PHP_*</code></td>
        <td><a anchor="fn-constants-and-masks-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Some\Any\CONST_*</code></td>
        <td><a anchor="fn-constants-and-masks-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>FOO*BAR</code></td>
        <td><a anchor="fn-constants-and-masks-3"><icon src="ko.svg"/><sup>3</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>*_SOME</code></td>
        <td><a anchor="fn-constants-and-masks-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><a anchor="fn-constants-and-masks-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Some\Any\*</code></td>
        <td><a anchor="fn-constants-and-masks-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><a anchor="fn-constants-and-masks-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>\*</code></td>
        <td><a anchor="fn-constants-and-masks-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><a anchor="fn-constants-and-masks-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo::*_BAZ</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-constants-and-masks-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo::B*R</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-constants-and-masks-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>$this::BAR</code></td>
        <td><a anchor="fn-constants-and-masks-5"><icon src="ko.svg"/><sup>5</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-constants-and-masks-6"><icon src="ko.svg"/><sup>6</sup></a></td>
    </tr>
    <tr>
        <td><code>$this::class</code></td>
        <td><a anchor="fn-constants-and-masks-5"><icon src="ko.svg"/><sup>5</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-constants-and-masks-6"><icon src="ko.svg"/><sup>6</sup></a></td>
    </tr>
</table>

<list type="decimal">
    <li id="fn-constants-and-masks-1">PHPStan has masks only for class constants.</li>
    <li id="fn-constants-and-masks-2">PHPStan has masks only for class constants, `Foo::BAR_*`, and none for global ones.</li>
    <li id="fn-constants-and-masks-3">PHPStan allows a star only at the end of a constant name.</li>
    <li id="fn-constants-and-masks-4">Psalm allows a star only at the end of a constant name.</li>
    <li id="fn-constants-and-masks-5">PHPStan does not allow `$this` before `::`.</li>
    <li id="fn-constants-and-masks-6">TypeLang does not allow `$this` before `::`.</li>
</list>

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
        <td><code>int[]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int[][]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(A|B)[]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array&lt;int&gt;[]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?int[]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int[]|null</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo::BAR[]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T[K]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T[int]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-lists-and-offsets-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T['a']</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-lists-and-offsets-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T[Foo::A]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-lists-and-offsets-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T[K|L]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-lists-and-offsets-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T[?K]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-lists-and-offsets-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(A|B)[K]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-lists-and-offsets-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(?T)[K]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-lists-and-offsets-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;int&gt;[K]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-lists-and-offsets-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>list&lt;int&gt;[0]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-lists-and-offsets-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int}['a']</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-lists-and-offsets-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T [K]</code></td>
        <td><a anchor="fn-lists-and-offsets-3"><icon src="ko.svg"/><sup>3</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int\n[]</code></td>
        <td><a anchor="fn-lists-and-offsets-3"><icon src="ko.svg"/><sup>3</sup></a></td>
        <td><a anchor="fn-lists-and-offsets-4"><icon src="warning.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>$this[]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-lists-and-offsets-5"><icon src="warning.svg"/><sup>5</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

<list type="decimal">
    <li id="fn-lists-and-offsets-1">Psalm accepts only declared templates.</li>
    <li id="fn-lists-and-offsets-2">Psalm supports offset access only on a template name, `T[K]`, and only with a template as the offset.</li>
    <li id="fn-lists-and-offsets-3">PHPStan does not allow a space before `[`.</li>
    <li id="fn-lists-and-offsets-4">Psalm glues it into the name.</li>
    <li id="fn-lists-and-offsets-5">Psalm resolves `$this` to `static`.</li>
</list>

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
                <format style="bold" color="RosyBrown">12/22</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">14/22</format>
            </warning>
        </td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">22/22</format>
            </note>
        </td>
    </tr>
    <tr>
        <td><code>A|B</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A|B|C</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A&amp;B</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A&amp;B&amp;C</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(A|B)&amp;C</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(A&amp;B)|C</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?int</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A&amp;B|C</code></td>
        <td><a anchor="fn-logical-types-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A|B&amp;C</code></td>
        <td><a anchor="fn-logical-types-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A&amp;B|C&amp;D</code></td>
        <td><a anchor="fn-logical-types-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?A|B</code></td>
        <td><a anchor="fn-logical-types-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?T|U[]</code></td>
        <td><a anchor="fn-logical-types-3"><icon src="ko.svg"/><sup>3</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A|?B</code></td>
        <td><a anchor="fn-logical-types-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?A&amp;B</code></td>
        <td><a anchor="fn-logical-types-5"><icon src="ko.svg"/><sup>5</sup></a></td>
        <td><a anchor="fn-logical-types-6"><icon src="warning.svg"/><sup>6</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A&amp;?B</code></td>
        <td><a anchor="fn-logical-types-7"><icon src="ko.svg"/><sup>7</sup></a></td>
        <td><a anchor="fn-logical-types-8"><icon src="warning.svg"/><sup>8</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?A&amp;?B</code></td>
        <td><a anchor="fn-logical-types-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><a anchor="fn-logical-types-9"><icon src="warning.svg"/><sup>9</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(?A)&amp;B</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-logical-types-10"><icon src="warning.svg"/><sup>10</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(A&amp;B)&amp;C</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-logical-types-9"><icon src="warning.svg"/><sup>9</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A[]|B[]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-logical-types-11"><icon src="warning.svg"/><sup>11</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A[]&amp;B[]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-logical-types-11"><icon src="warning.svg"/><sup>11</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T|U[K]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(A|?B)[]</code></td>
        <td><a anchor="fn-logical-types-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><a anchor="fn-logical-types-9"><icon src="warning.svg"/><sup>9</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

<list type="decimal">
    <li id="fn-logical-types-1">PHPStan rejects it on purpose: it is stricter and requires parentheses, because the priority may be non-obvious.</li>
    <li id="fn-logical-types-2">PHPStan rejects it on purpose: it is stricter and requires parentheses, because the priority may be non-obvious: `(?A)|B` or `?(A|B)`.</li>
    <li id="fn-logical-types-3">PHPStan rejects it on purpose: it is stricter and requires parentheses, because the priority may be non-obvious: `(?T)|U[]`, `?(T|U[])` or `?(T|U)[]`.</li>
    <li id="fn-logical-types-4">PHPStan rejects it on purpose: it is stricter and requires parentheses, because the priority may be non-obvious: `A|(?B)`.</li>
    <li id="fn-logical-types-5">PHPStan rejects it on purpose: it is stricter and requires parentheses, because the priority may be non-obvious: `(?A)&B` or `?(A&B)`.</li>
    <li id="fn-logical-types-6">Psalm reads `null|(A&B)`, the mark binds weaker there.</li>
    <li id="fn-logical-types-7">PHPStan rejects it on purpose: it is stricter and requires parentheses, because the priority may be non-obvious: `A&(?B)`.</li>
    <li id="fn-logical-types-8">Psalm silently drops the `null`.</li>
    <li id="fn-logical-types-9">Psalm loses `B`.</li>
    <li id="fn-logical-types-10">The only form PHPStan accepts. Psalm drops the `null`.</li>
    <li id="fn-logical-types-11">Psalm merges the arrays into one.</li>
</list>

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
        <td><code>Foo\Bar&lt;int&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array&lt;int, string&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;int&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>iterable&lt;int&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>list&lt;int&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>non-empty-list&lt;int&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;int, string,&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-generics-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;*&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-generics-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;int, *&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-generics-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;covariant T&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-generics-3"><icon src="warning.svg"/><sup>3</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;contravariant T&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-generics-3"><icon src="warning.svg"/><sup>3</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;invariant T&gt;</code></td>
        <td><a anchor="fn-generics-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;int&gt;[K]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-generics-5"><icon src="ko.svg"/><sup>5</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;T is int ? A : B&gt;</code></td>
        <td><a anchor="fn-generics-6"><icon src="ko.svg"/><sup>6</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

<list type="decimal">
    <li id="fn-generics-1">Psalm does not allow a trailing comma in generic arguments.</li>
    <li id="fn-generics-2">Psalm has no wildcard `*` as a generic argument.</li>
    <li id="fn-generics-3">Psalm drops the keyword silently.</li>
    <li id="fn-generics-4">PHPStan knows only the `covariant` and `contravariant` keywords.</li>
    <li id="fn-generics-5">Psalm supports offset access only on a template name, `T[K]`, and only with a template as the offset.</li>
    <li id="fn-generics-6">PHPStan requires parentheses around a conditional type, even as a generic argument.</li>
</list>

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
        <td><code>callable&lt;T&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-templates-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T, U,&gt;(T): U</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-templates-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T of int&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-templates-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T as int&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-templates-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T super int&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-templates-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T of A super B&gt;(): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-templates-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T = int&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-templates-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T of int = int&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-templates-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;covariant T&gt;(T): T</code></td>
        <td><a anchor="fn-templates-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><a anchor="fn-templates-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Closure&lt;T&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-templates-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>pure-Closure&lt;T&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-templates-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;T&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-templates-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

<list type="decimal">
    <li id="fn-templates-1">Psalm has no template parameters for callables.</li>
    <li id="fn-templates-2">PHPStan has no variance on template parameters of a callable.</li>
</list>

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
                <format style="bold" color="RosyBrown">19/32</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">14/32</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">31/32</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td><code>callable(): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int $a): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int ...$a): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Closure(int): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>pure-callable(int): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int)</code></td>
        <td><a anchor="fn-callable-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(...int): void</code></td>
        <td><a anchor="fn-callable-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int...): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int... $a): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-callable-3"><icon src="ko.svg"/><sup>3</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(...): void</code></td>
        <td><a anchor="fn-callable-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-callable-5"><icon src="ko.svg"/><sup>5</sup></a></td>
    </tr>
    <tr>
        <td><code>callable(...$a): void</code></td>
        <td><a anchor="fn-callable-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><a anchor="fn-callable-6"><icon src="ko.svg"/><sup>6</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable($a): void</code></td>
        <td><a anchor="fn-callable-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><a anchor="fn-callable-6"><icon src="ko.svg"/><sup>6</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int &amp;$a): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-callable-7"><icon src="ko.svg"/><sup>7</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(&amp;$a): void</code></td>
        <td><a anchor="fn-callable-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><a anchor="fn-callable-6"><icon src="ko.svg"/><sup>6</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int&amp;): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-callable-8"><icon src="warning.svg"/><sup>8</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int &amp;...$a): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-callable-8"><icon src="warning.svg"/><sup>8</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int $a=): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-callable-9"><icon src="ko.svg"/><sup>9</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int=): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int = 42): void</code></td>
        <td><a anchor="fn-callable-10"><icon src="ko.svg"/><sup>10</sup></a></td>
        <td><a anchor="fn-callable-11"><icon src="warning.svg"/><sup>11</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int $a = 42): void</code></td>
        <td><a anchor="fn-callable-12"><icon src="ko.svg"/><sup>12</sup></a></td>
        <td><a anchor="fn-callable-13"><icon src="ko.svg"/><sup>13</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int $a = -1): void</code></td>
        <td><a anchor="fn-callable-10"><icon src="ko.svg"/><sup>10</sup></a></td>
        <td><a anchor="fn-callable-13"><icon src="ko.svg"/><sup>13</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int $a = PHP_INT_MAX): void</code></td>
        <td><a anchor="fn-callable-10"><icon src="ko.svg"/><sup>10</sup></a></td>
        <td><a anchor="fn-callable-13"><icon src="ko.svg"/><sup>13</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int $a = Foo::BAR): void</code></td>
        <td><a anchor="fn-callable-10"><icon src="ko.svg"/><sup>10</sup></a></td>
        <td><a anchor="fn-callable-13"><icon src="ko.svg"/><sup>13</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(): $this</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-callable-14"><icon src="warning.svg"/><sup>14</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(): T is int ? A : B</code></td>
        <td><a anchor="fn-callable-15"><icon src="ko.svg"/><sup>15</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>foo(T): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-callable-16"><icon src="ko.svg"/><sup>16</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable (int): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(): T|U</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(): T&amp;U</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-callable-17"><icon src="warning.svg"/><sup>17</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(): ?T|U</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-callable-17"><icon src="warning.svg"/><sup>17</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(): callable(): T|U</code></td>
        <td><a anchor="fn-callable-18"><icon src="ko.svg"/><sup>18</sup></a></td>
        <td><a anchor="fn-callable-18"><icon src="warning.svg"/><sup>18</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

<list type="decimal">
    <li id="fn-callable-1">PHPStan rejects it on purpose: it requires a return type for strict signatures, and it avoids the conflict with a variance in front of a group, `Generic<covariant(T)>` against `Generic<covariant(T): void>`.</li>
    <li id="fn-callable-2">PHPStan supports only the postfix form, `callable(int...): void`.</li>
    <li id="fn-callable-3">Psalm does not allow a name after `...`.</li>
    <li id="fn-callable-4">PHPStan rejects it on purpose: it requires a type on every parameter.</li>
    <li id="fn-callable-5">TypeLang requires a type or a name after `...`.</li>
    <li id="fn-callable-6">Psalm requires a type for every parameter.</li>
    <li id="fn-callable-7">Psalm has no by-reference marker `&` with a parameter name.</li>
    <li id="fn-callable-8">Psalm drops the `&`.</li>
    <li id="fn-callable-9">Psalm has no optional marker `=` after a parameter name.</li>
    <li id="fn-callable-10">PHPStan has the optional marker `=`, but no default value after it.</li>
    <li id="fn-callable-11">Psalm skips it and loses the value.</li>
    <li id="fn-callable-12">PHPStan has no default values of parameters, only the bare `=`.</li>
    <li id="fn-callable-13">Psalm has no default values of parameters.</li>
    <li id="fn-callable-14">Psalm resolves it to `static`.</li>
    <li id="fn-callable-15">PHPStan requires parentheses around a conditional return type.</li>
    <li id="fn-callable-16">Psalm reads only `callable` and `Closure` before the parentheses of a callable.</li>
    <li id="fn-callable-17">Psalm folds it into the return type.</li>
    <li id="fn-callable-18">PHPStan rejects it, Psalm folds the union.</li>
</list>

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
        <td><code>array{}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a?: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{int, string}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{0: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{'a': int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: array{b: int}}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int, ...}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>list{int, string}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>object{a: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{int, string,}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-shapes-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int, string}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-shapes-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{Foo::BAR: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-shapes-3"><icon src="ko.svg"/><sup>3</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{covariant: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-shapes-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{'Foo\Bar': int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-shapes-5"><icon src="warning.svg"/><sup>5</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{- 1: int}</code></td>
        <td><a anchor="fn-shapes-6"><icon src="ko.svg"/><sup>6</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{...}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-shapes-7"><icon src="warning.svg"/><sup>7</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int, ...&lt;string&gt;}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-shapes-8"><icon src="ko.svg"/><sup>8</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int, ...&lt;string, int&gt;}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>list{1: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-shapes-9"><icon src="ko.svg"/><sup>9</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>list{...}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-shapes-10"><icon src="warning.svg"/><sup>10</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>non-empty-array{a: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-shapes-11"><icon src="ko.svg"/><sup>11</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>object{a: int,}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-shapes-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>object{a: int, ...}</code></td>
        <td><a anchor="fn-shapes-12"><icon src="ko.svg"/><sup>12</sup></a></td>
        <td><a anchor="fn-shapes-13"><icon src="warning.svg"/><sup>13</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>object{0: int}</code></td>
        <td><a anchor="fn-shapes-14"><icon src="ko.svg"/><sup>14</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>object{Foo::BAR: int}</code></td>
        <td><a anchor="fn-shapes-14"><icon src="ko.svg"/><sup>14</sup></a></td>
        <td><a anchor="fn-shapes-3"><icon src="ko.svg"/><sup>3</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo{a: int}</code></td>
        <td><a anchor="fn-shapes-15"><icon src="ko.svg"/><sup>15</sup></a></td>
        <td><a anchor="fn-shapes-16"><icon src="ko.svg"/><sup>16</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int} | array{b: string}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-shapes-17"><icon src="warning.svg"/><sup>17</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

<list type="decimal">
    <li id="fn-shapes-1">Psalm does not allow a trailing comma in a shape.</li>
    <li id="fn-shapes-2">Psalm does not allow named and positional fields in one shape.</li>
    <li id="fn-shapes-3">Psalm does not allow a class constant as a key.</li>
    <li id="fn-shapes-4">Psalm does not accept a reserved word as a key.</li>
    <li id="fn-shapes-5">Psalm drops the backslash from a string key.</li>
    <li id="fn-shapes-6">PHPStan does not allow a space after the sign of a key.</li>
    <li id="fn-shapes-7">Psalm reads an empty array.</li>
    <li id="fn-shapes-8">Psalm does not read a single type argument in the unsealed marker.</li>
    <li id="fn-shapes-9">Psalm does not allow explicit keys in a list shape.</li>
    <li id="fn-shapes-10">Psalm loses the tail.</li>
    <li id="fn-shapes-11">Psalm has no shape for `non-empty-array`.</li>
    <li id="fn-shapes-12">PHPStan has no unsealed object shape.</li>
    <li id="fn-shapes-13">Psalm drops `...`.</li>
    <li id="fn-shapes-14">PHPStan allows only names as the keys of an object shape.</li>
    <li id="fn-shapes-15">PHPStan allows a shape only after `array`, `list` and `object`.</li>
    <li id="fn-shapes-16">Psalm allows a shape only after `array`, `list` and `object`.</li>
    <li id="fn-shapes-17">Psalm merges them into one.</li>
</list>

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
        <td><code>(T is int ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(T is not int ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>($a is int ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T is int ? A : B</code></td>
        <td><a anchor="fn-conditional-types-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><a anchor="fn-conditional-types-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T is not int ? A : B</code></td>
        <td><a anchor="fn-conditional-types-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><a anchor="fn-conditional-types-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>$a is int ? A : B</code></td>
        <td><a anchor="fn-conditional-types-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><a anchor="fn-conditional-types-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(T is ?int ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-conditional-types-3"><icon src="ko.svg"/><sup>3</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(T is int[] ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-conditional-types-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(T is callable(int): void ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-conditional-types-5"><icon src="ko.svg"/><sup>5</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(T is int ? A : B is C ? D : E)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-conditional-types-6"><icon src="ko.svg"/><sup>6</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(T is int ? A : (B is C ? D : E))</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-conditional-types-7"><icon src="ko.svg"/><sup>7</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(T is $this ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-conditional-types-8"><icon src="warning.svg"/><sup>8</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>($this is int ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-conditional-types-9"><icon src="ko.svg"/><sup>9</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int &amp;$a): ($a is int ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-conditional-types-10"><icon src="ko.svg"/><sup>10</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: T is int ? A : B}</code></td>
        <td><a anchor="fn-conditional-types-11"><icon src="ko.svg"/><sup>11</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(T $a): T is int ? A : B</code></td>
        <td><a anchor="fn-conditional-types-12"><icon src="ko.svg"/><sup>12</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?T is U ? V : Z</code></td>
        <td><a anchor="fn-conditional-types-13"><icon src="ko.svg"/><sup>13</sup></a></td>
        <td><a anchor="fn-conditional-types-14"><icon src="warning.svg"/><sup>14</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A|B is C ? D : E</code></td>
        <td><a anchor="fn-conditional-types-15"><icon src="ko.svg"/><sup>15</sup></a></td>
        <td><a anchor="fn-conditional-types-15"><icon src="ko.svg"/><sup>15</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

<list type="decimal">
    <li id="fn-conditional-types-1">PHPStan requires parentheses around a conditional type.</li>
    <li id="fn-conditional-types-2">Psalm requires parentheses around a conditional type.</li>
    <li id="fn-conditional-types-3">Psalm does not allow a nullable type as the target of a condition.</li>
    <li id="fn-conditional-types-4">Psalm does not allow a list type as the target of a condition.</li>
    <li id="fn-conditional-types-5">Psalm does not allow a callable as the target of a condition.</li>
    <li id="fn-conditional-types-6">Psalm does not allow a conditional type in the else branch without parentheses.</li>
    <li id="fn-conditional-types-7">Psalm does not allow a nested condition in the else branch, even in parentheses.</li>
    <li id="fn-conditional-types-8">Psalm resolves `$this` to `static`.</li>
    <li id="fn-conditional-types-9">Psalm does not allow `$this` as the subject of a condition.</li>
    <li id="fn-conditional-types-10">Psalm has no by-reference marker `&` with a parameter name.</li>
    <li id="fn-conditional-types-11">PHPStan requires parentheses around a conditional type inside a shape.</li>
    <li id="fn-conditional-types-12">PHPStan requires parentheses around a conditional return type.</li>
    <li id="fn-conditional-types-13">PHPStan does not allow `?` in front of the subject of a condition.</li>
    <li id="fn-conditional-types-14">Psalm reads it as `(T is U:fn-corpus as mixed ? V:fn-corpus as mixed : Z)|null`.</li>
    <li id="fn-conditional-types-15">TypeLang binds the condition weaker than the union, the others reject it.</li>
</list>

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
        <td><code>int&lt;0, max&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int&lt;min, 0&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>class-string&lt;Foo&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>key-of&lt;T&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>value-of&lt;T&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int-mask&lt;1, 2, 4&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int&lt;10, 0&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-special-names-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int&lt;PHP_INT_MIN, PHP_INT_MAX&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-special-names-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int-mask&lt;1, 2, 4,&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-special-names-3"><icon src="ko.svg"/><sup>3</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int-mask-of&lt;1|2|4&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-special-names-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>class-string&lt;int&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-special-names-5"><icon src="ko.svg"/><sup>5</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>enum-string&lt;Foo&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-special-names-6"><icon src="warning.svg"/><sup>6</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>trait-string&lt;Foo&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-special-names-7"><icon src="ko.svg"/><sup>7</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>key-of&lt;T, U&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-special-names-8"><icon src="warning.svg"/><sup>8</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>uppercase-string</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-special-names-9"><icon src="warning.svg"/><sup>9</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>open-resource</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-special-names-10"><icon src="ko.svg"/><sup>10</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

<list type="decimal">
    <li id="fn-special-names-1">Psalm checks an integer range while parsing: the minimum must not exceed the maximum.</li>
    <li id="fn-special-names-2">Psalm accepts only `min`, `max` and integer literals as the bounds of a range.</li>
    <li id="fn-special-names-3">Psalm does not allow a trailing comma in `int-mask`.</li>
    <li id="fn-special-names-4">Psalm requires a class constant mask as the argument of `int-mask-of`.</li>
    <li id="fn-special-names-5">Psalm checks while parsing that the argument of `class-string` is a class.</li>
    <li id="fn-special-names-6">Psalm turns it into `class-string`.</li>
    <li id="fn-special-names-7">Psalm has no `trait-string`.</li>
    <li id="fn-special-names-8">Psalm drops the second argument.</li>
    <li id="fn-special-names-9">Psalm drops the refinement.</li>
    <li id="fn-special-names-10">Psalm has no `open-resource`.</li>
</list>

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
                <format style="bold" color="RosyBrown">11/21</format>
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
        <td><code>\nint</code></td>
        <td><a anchor="fn-whitespace-and-comments-1"><icon src="ko.svg"/><sup>1</sup></a></td>
        <td><a anchor="fn-whitespace-and-comments-2"><icon src="ko.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int |\n string</code></td>
        <td><a anchor="fn-whitespace-and-comments-3"><icon src="ko.svg"/><sup>3</sup></a></td>
        <td><a anchor="fn-whitespace-and-comments-4"><icon src="ko.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array&lt;\n    int,\n    string\n&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-whitespace-and-comments-5"><icon src="ko.svg"/><sup>5</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int\n$a): void</code></td>
        <td><a anchor="fn-whitespace-and-comments-6"><icon src="ko.svg"/><sup>6</sup></a></td>
        <td><a anchor="fn-whitespace-and-comments-7"><icon src="ko.svg"/><sup>7</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array {a: int}</code></td>
        <td><a anchor="fn-whitespace-and-comments-8"><icon src="ko.svg"/><sup>8</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int []</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int [ ]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T [K]</code></td>
        <td><a anchor="fn-whitespace-and-comments-9"><icon src="ko.svg"/><sup>9</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo &lt;int&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array &lt;K, V&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo :: BAR</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>? int</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int | string</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable( int ): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int , string): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T&gt; (T): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-whitespace-and-comments-10"><icon src="ko.svg"/><sup>10</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int /* c */</code></td>
        <td><a anchor="fn-whitespace-and-comments-11"><icon src="ko.svg"/><sup>11</sup></a></td>
        <td><a anchor="fn-whitespace-and-comments-12"><icon src="ko.svg"/><sup>12</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int // c</code></td>
        <td><a anchor="fn-whitespace-and-comments-11"><icon src="ko.svg"/><sup>11</sup></a></td>
        <td><a anchor="fn-whitespace-and-comments-12"><icon src="ko.svg"/><sup>12</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int # c</code></td>
        <td><a anchor="fn-whitespace-and-comments-11"><icon src="ko.svg"/><sup>11</sup></a></td>
        <td><a anchor="fn-whitespace-and-comments-12"><icon src="ko.svg"/><sup>12</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int /* c */, b: string}</code></td>
        <td><a anchor="fn-whitespace-and-comments-11"><icon src="ko.svg"/><sup>11</sup></a></td>
        <td><a anchor="fn-whitespace-and-comments-12"><icon src="ko.svg"/><sup>12</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Some&lt;/* a */int/* b */,/* c */string&gt;</code></td>
        <td><a anchor="fn-whitespace-and-comments-11"><icon src="ko.svg"/><sup>11</sup></a></td>
        <td><a anchor="fn-whitespace-and-comments-12"><icon src="ko.svg"/><sup>12</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

<list type="decimal">
    <li id="fn-whitespace-and-comments-1">PHPStan does not allow a line break at the very start.</li>
    <li id="fn-whitespace-and-comments-2">Psalm does not allow a line break at the very start.</li>
    <li id="fn-whitespace-and-comments-3">PHPStan does not allow a line break after an operator.</li>
    <li id="fn-whitespace-and-comments-4">Psalm does not allow a line break after an operator.</li>
    <li id="fn-whitespace-and-comments-5">Psalm does not allow line breaks inside generic arguments.</li>
    <li id="fn-whitespace-and-comments-6">PHPStan does not allow a line break between a type and a parameter name.</li>
    <li id="fn-whitespace-and-comments-7">Psalm does not allow a line break between a type and a parameter name.</li>
    <li id="fn-whitespace-and-comments-8">PHPStan does not allow a space before `{` of a shape.</li>
    <li id="fn-whitespace-and-comments-9">PHPStan does not allow a space before `[`.</li>
    <li id="fn-whitespace-and-comments-10">Psalm has no template parameters for callables.</li>
    <li id="fn-whitespace-and-comments-11">PHPStan does not allow comments in a type.</li>
    <li id="fn-whitespace-and-comments-12">Psalm does not allow comments in a type.</li>
</list>

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
        <td><code>int string</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-1"><icon src="warning.svg"/><sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;A B&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-2"><icon src="warning.svg"/><sup>2</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int b: string}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-3"><icon src="warning.svg"/><sup>3</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-4"><icon src="warning.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int|</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-5"><icon src="warning.svg"/><sup>5</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A&amp;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-5"><icon src="warning.svg"/><sup>5</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A&amp;&amp;B</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-6"><icon src="warning.svg"/><sup>6</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(A|)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-5"><icon src="warning.svg"/><sup>5</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>??int</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-7"><icon src="warning.svg"/><sup>7</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int?[]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-8"><icon src="warning.svg"/><sup>8</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int ? a</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-9"><icon src="warning.svg"/><sup>9</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int *</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-10"><icon src="warning.svg"/><sup>10</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>PHP_ *</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-11"><icon src="warning.svg"/><sup>11</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>in t</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-12"><icon src="warning.svg"/><sup>12</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>'a</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-13"><icon src="warning.svg"/><sup>13</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>'a''b'</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-14"><icon src="warning.svg"/><sup>14</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo\</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-15"><icon src="warning.svg"/><sup>15</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo\\Bar</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-16"><icon src="warning.svg"/><sup>16</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>foo-</code></td>
        <td><a anchor="fn-malformed-input-17"><icon src="warning.svg"/><sup>17</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>08</code></td>
        <td><a anchor="fn-malformed-input-18"><icon src="warning.svg"/><sup>18</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>1__000</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-19"><icon src="warning.svg"/><sup>19</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>1._5</code></td>
        <td><a anchor="fn-malformed-input-20"><icon src="warning.svg"/><sup>20</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo::BAR::class</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-21"><icon src="warning.svg"/><sup>21</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(Foo)::class</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-22"><icon src="warning.svg"/><sup>22</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo::BAR-BAZ</code></td>
        <td><a anchor="fn-malformed-input-23"><icon src="warning.svg"/><sup>23</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;T of Bar&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-24"><icon src="warning.svg"/><sup>24</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;out T&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-25"><icon src="warning.svg"/><sup>25</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int, ...int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-4"><icon src="warning.svg"/><sup>4</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{$a: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-26"><icon src="warning.svg"/><sup>26</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{PHP_*: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-26"><icon src="warning.svg"/><sup>26</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int, a: string}</code></td>
        <td><a anchor="fn-malformed-input-27"><icon src="warning.svg"/><sup>27</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-27"><icon src="warning.svg"/><sup>27</sup></a></td>
    </tr>
    <tr>
        <td><code>(T is not not int ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-3"><icon src="warning.svg"/><sup>3</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T is int ? A</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-28"><icon src="warning.svg"/><sup>28</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A is B ? C</code></td>
        <td><icon src="ok.svg"/></td>
        <td><a anchor="fn-malformed-input-29"><icon src="warning.svg"/><sup>29</sup></a></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

<list type="decimal">
    <li id="fn-malformed-input-1">Psalm glues them into `intstring`.</li>
    <li id="fn-malformed-input-2">Psalm glues them.</li>
    <li id="fn-malformed-input-3">Psalm glues the words.</li>
    <li id="fn-malformed-input-4">Psalm ignores it.</li>
    <li id="fn-malformed-input-5">Psalm ignores a dangling operator and reads the rest.</li>
    <li id="fn-malformed-input-6">Psalm reads `A&B`.</li>
    <li id="fn-malformed-input-7">Psalm folds it.</li>
    <li id="fn-malformed-input-8">Psalm drops the mark and the brackets.</li>
    <li id="fn-malformed-input-9">Psalm ignores the tail.</li>
    <li id="fn-malformed-input-10">Psalm accepts a trailing `*`.</li>
    <li id="fn-malformed-input-11">Psalm glues it into `PHP_*`.</li>
    <li id="fn-malformed-input-12">Psalm reads `int`.</li>
    <li id="fn-malformed-input-13">Psalm accepts and truncates it.</li>
    <li id="fn-malformed-input-14">Psalm keeps the first.</li>
    <li id="fn-malformed-input-15">Psalm accepts and ignores it.</li>
    <li id="fn-malformed-input-16">Psalm accepts a doubled backslash as part of a name.</li>
    <li id="fn-malformed-input-17">PHPStan accepts a trailing hyphen in a name.</li>
    <li id="fn-malformed-input-18">PHPStan accepts a digit outside of the octal range after a leading zero.</li>
    <li id="fn-malformed-input-19">Psalm accepts a doubled digit separator.</li>
    <li id="fn-malformed-input-20">PHPStan accepts a separator right after the decimal point.</li>
    <li id="fn-malformed-input-21">Psalm drops the second constant.</li>
    <li id="fn-malformed-input-22">Psalm drops the constant.</li>
    <li id="fn-malformed-input-23">PHPStan accepts a hyphen in a constant name.</li>
    <li id="fn-malformed-input-24">Psalm reads a template map.</li>
    <li id="fn-malformed-input-25">Psalm glues the words into `outT`.</li>
    <li id="fn-malformed-input-26">Psalm reads a string key.</li>
    <li id="fn-malformed-input-27">Psalm rejects it, PHPStan and TypeLang keep both.</li>
    <li id="fn-malformed-input-28">Psalm drops the condition.</li>
    <li id="fn-malformed-input-29">Psalm reads `A`.</li>
</list>

