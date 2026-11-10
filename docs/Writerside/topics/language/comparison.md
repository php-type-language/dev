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
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>$this</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?$this</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

1. PHPStan allows it anywhere, Psalm only for known pseudo types.
2. Psalm resolves it to `static`.

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
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>- 1</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="warning.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>007</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>0x1F</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>-0x1F</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>0b101</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>0o17</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>1e3</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>1_000.5</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>.5</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>1.</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>1.5e3</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>9223372036854775808</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>-9223372036854775809</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>4</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>'a"b'</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>"a\"b"</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>5</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>"a\nb"</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>6</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>"a\x41"</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>6</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>"a\u{1F600}"</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>6</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

1. Psalm rejects it.
2. PHPStan rejects it, Psalm reads it with distortion.
3. Psalm clamps it to the maximum.
4. Psalm clamps it to the minimum.
5. Psalm keeps the backslash in the value.
6. Psalm does not decode it.

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
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Some\Any\CONST_*</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>FOO*BAR</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>*_SOME</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Some\Any\*</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>\*</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo::*_BAZ</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo::B*R</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>$this::BAR</code></td>
        <td><icon src="ko.svg"/><sup>4</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>4</sup></td>
    </tr>
    <tr>
        <td><code>$this::class</code></td>
        <td><icon src="ko.svg"/><sup>4</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>4</sup></td>
    </tr>
</table>

1. PHPStan stops at the star.
2. PHPStan rejects it.
3. Psalm rejects it.
4. Only Psalm reads it.

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
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T['a']</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T[Foo::A]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T[K|L]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T[?K]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(A|B)[K]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(?T)[K]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;int&gt;[K]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>list&lt;int&gt;[0]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int}['a']</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T [K]</code></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int\n[]</code></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="warning.svg"/><sup>4</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>$this[]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>5</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

1. Psalm accepts only declared templates.
2. Psalm rejects it.
3. PHPStan rejects it.
4. Psalm glues it into the name.
5. Psalm resolves `$this` to `static`.

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
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A|B&amp;C</code></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A&amp;B|C&amp;D</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?A|B</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?T|U</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?T|U[]</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A|?B</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?A&amp;B</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="warning.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A&amp;?B</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="warning.svg"/><sup>4</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?A&amp;?B</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="warning.svg"/><sup>5</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(?A)&amp;B</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>6</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(A&amp;B)&amp;C</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>5</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(int|string)&amp;bool</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>7</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A[]|B[]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>8</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A[]&amp;B[]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>8</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A&amp;B[]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>7</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A|B[K]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>7</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(A|?B)[]</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="warning.svg"/><sup>5</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

1. PHPStan rejects them.
2. PHPStan rejects it.
3. Psalm reads `null|(A&B)`, the mark binds weaker there.
4. Psalm silently drops the `null`.
5. Psalm loses `B`.
6. The only form PHPStan accepts. Psalm drops the `null`.
7. Psalm rejects it.
8. Psalm merges the arrays into one.

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
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;*&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;int, *&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;covariant T&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;contravariant T&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;invariant T&gt;</code></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;int&gt;[K]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;T is int ? A : B&gt;</code></td>
        <td><icon src="ko.svg"/><sup>4</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

1. Psalm rejects it.
2. Psalm drops the keyword silently.
3. Only TypeLang reads it.
4. PHPStan rejects it.

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
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T, U,&gt;(T): U</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T of int&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T as int&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T super int&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T of A super B&gt;(): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T = int&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T of int = int&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;covariant T&gt;(T): T</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Closure&lt;T&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>pure-Closure&lt;T&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;T&gt;(T): T</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

1. Psalm rejects it.
2. Only TypeLang reads it.

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
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(...int): void</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int... $a): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(...): void</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>4</sup></td>
    </tr>
    <tr>
        <td><code>callable(...$a): void</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable($a): void</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int &amp;$a): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(&amp;$a): void</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int&amp;): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>5</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int &amp;...$a): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>5</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int $a=): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int=)</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int = 42): void</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="warning.svg"/><sup>6</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int $a = 42): void</code></td>
        <td><icon src="ko.svg"/><sup>7</sup></td>
        <td><icon src="ko.svg"/><sup>7</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int $a = -1): void</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int $a = PHP_INT_MAX): void</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int $a = Foo::BAR): void</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(): $this</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>8</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(): T is int ? A : B</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>foo(T): void</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
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
        <td><icon src="warning.svg"/><sup>9</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(): ?T|U</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>9</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(): callable(): T|U</code></td>
        <td><icon src="ko.svg"/><sup>10</sup></td>
        <td><icon src="warning.svg"/><sup>10</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

1. PHPStan requires it.
2. PHPStan rejects it.
3. Psalm rejects it.
4. TypeLang rejects it.
5. Psalm drops the `&`.
6. Psalm skips it and loses the value.
7. Neither PHPStan nor Psalm parses it.
8. Psalm resolves it to `static`.
9. Psalm folds it into the return type.
10. PHPStan rejects it, Psalm folds the union.

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
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int, string}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{Foo::BAR: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{covariant: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{'Foo\Bar': int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{- 1: int}</code></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{...}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>4</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int, ...&lt;string&gt;}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
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
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>list{...}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>5</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>non-empty-array{a: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>object{a: int,}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>object{a: int, ...}</code></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="warning.svg"/><sup>6</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>object{0: int}</code></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>object{Foo::BAR: int}</code></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo{a: int}</code></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int} | array{b: string}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>7</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

1. Psalm rejects it.
2. Psalm drops it.
3. PHPStan rejects it.
4. Psalm reads an empty array.
5. Psalm loses the tail.
6. Psalm drops `...`.
7. Psalm merges them into one.

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
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T is not int ? A : B</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>$a is int ? A : B</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(T is ?int ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(T is int[] ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(T is callable(int): void ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(T is int ? A : B is C ? D : E)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>4</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(T is int ? A : (B is C ? D : E))</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(T is $this ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>5</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>($this is int ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int &amp;$a): ($a is int ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: T is int ? A : B}</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(T $a): T is int ? A : B</code></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>?T is U ? V : Z</code></td>
        <td><icon src="ko.svg"/><sup>6</sup></td>
        <td><icon src="warning.svg"/><sup>6</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A|B is C ? D : E</code></td>
        <td><icon src="ko.svg"/><sup>7</sup></td>
        <td><icon src="ko.svg"/><sup>7</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

1. PHPStan and Psalm require them.
2. PHPStan rejects it.
3. Psalm rejects it.
4. Only PHPStan and TypeLang read it.
5. Psalm resolves `$this` to `static`.
6. Psalm moves `null` out of the condition, PHPStan rejects it.
7. TypeLang binds the condition weaker than the union, the others reject it.

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
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int&lt;PHP_INT_MIN, PHP_INT_MAX&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int-mask&lt;1, 2, 4,&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int-mask-of&lt;1|2|4&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>class-string&lt;int&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>enum-string&lt;Foo&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>trait-string&lt;Foo&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>key-of&lt;T, U&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>uppercase-string</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>4</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>open-resource</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

1. Psalm rejects it.
2. Psalm turns it into `class-string`.
3. Psalm drops the second argument.
4. Psalm drops the refinement.

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
        <td><code>\nint</code></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int |\n string</code></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array&lt;\n    int,\n    string\n&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int\n$a): void</code></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array {a: int}</code></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
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
        <td><icon src="ko.svg"/><sup>1</sup></td>
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
        <td><code>callable( int )</code></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable(int , string)</code></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>callable&lt;T&gt; (T)</code></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ko.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int /* c */</code></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int // c</code></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int # c</code></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int /* c */, b: string}</code></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Some&lt;/* a */int/* b */,/* c */string&gt;</code></td>
        <td><icon src="ko.svg"/><sup>1</sup></td>
        <td><icon src="ko.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

1. PHPStan rejects it.
2. Psalm rejects it.
3. PHPStan and Psalm reject it.

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
        <td><icon src="warning.svg"/><sup>1</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;A B&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>2</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int b: string}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>4</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int|</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>5</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A&amp;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>5</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A&amp;&amp;B</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>6</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(A|)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>5</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>??int</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>7</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int?[]</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>8</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int ? a</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>9</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>int *</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>10</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>PHP_ *</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>11</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>in t</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>12</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>'a</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>13</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>'a''b'</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>14</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo\</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>15</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo\\Bar</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>16</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>foo-</code></td>
        <td><icon src="warning.svg"/><sup>17</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>08</code></td>
        <td><icon src="warning.svg"/><sup>17</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>1__000</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>18</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>1._5</code></td>
        <td><icon src="warning.svg"/><sup>17</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo::BAR::class</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>19</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>(Foo)::class</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>20</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo::BAR-BAZ</code></td>
        <td><icon src="warning.svg"/><sup>17</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;T of Bar&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>21</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>Foo&lt;out T&gt;</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>22</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int, ...int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>4</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{$a: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>23</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{PHP_*: int}</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>23</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>array{a: int, a: string}</code></td>
        <td><icon src="warning.svg"/><sup>24</sup></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>24</sup></td>
    </tr>
    <tr>
        <td><code>(T is not not int ? A : B)</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>3</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>T is int ? A</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>25</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td><code>A is B ? C</code></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/><sup>26</sup></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>

1. Psalm glues them into `intstring`.
2. Psalm glues them.
3. Psalm glues the words.
4. Psalm ignores it.
5. Psalm drops it.
6. Psalm reads `A&B`.
7. Psalm folds it.
8. Psalm drops the mark and the brackets.
9. Psalm ignores the tail.
10. Psalm accepts a trailing `*`.
11. Psalm glues it into `PHP_*`.
12. Psalm reads `int`.
13. Psalm accepts and truncates it.
14. Psalm keeps the first.
15. Psalm accepts and ignores it.
16. Psalm reads it as `Foo\\Bar`.
17. PHPStan accepts it.
18. Psalm accepts it.
19. Psalm drops the second constant.
20. Psalm drops the constant.
21. Psalm reads a template map.
22. Psalm glues the words into `outT`.
23. Psalm reads a string key.
24. Psalm rejects it, PHPStan and TypeLang keep both.
25. Psalm drops the condition.
26. Psalm reads `A`.

