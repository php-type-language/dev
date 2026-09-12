# Syntax Comparison

<show-structure for="chapter" depth="2"/>

The language is defined by a syntax that is based on the grammar of popular
static code analysis tools: [PHPStan](https://phpstan.org/) and [Psalm](https://psalm.dev/).

- PHPStan: [https://phpstan.org](https://phpstan.org/writing-php-code/phpdoc-types)
- Psalm: [https://psalm.dev](https://psalm.dev/docs/annotating_code/type_syntax/atomic_types/)
- Phan: [https://github.com/phan](https://github.com/phan/phan/wiki/About-Union-Types)
- phpDocumentor: [https://docs.phpdoc.org](https://docs.phpdoc.org/guide/guides/types.html)

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
        <td>
            <icon src="phan.png" height="20"/>&nbsp;<a href="https://github.com/phan/phan">Phan</a>
        </td>
        <td>
            <icon src="phpdocumentor.png" height="20"/>&nbsp;<a href="https://github.com/phpDocumentor/TypeResolver">phpDocumentor</a>
        </td>
    </tr>
    <tr>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">82/83</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">40/83</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">69/83</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">31/83</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">62/83</format>
            </warning>
        </td>
    </tr>
</table>

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
            <icon src="typelang.svg" height="20"/>&nbsp;TypeLang
        </td>
        <td><code>type-lang/parser</code> 2.x</td>
        <td><code>TypeParser::parse()</code> returns a node</td>
    </tr>
    <tr>
        <td>
            <icon src="psalm.png" height="20"/>&nbsp;Psalm
        </td>
        <td><code>vimeo/psalm</code> 6.5</td>
        <td><code>Psalm\Type::parseString()</code> returns a type carrying what was written</td>
    </tr>
    <tr>
        <td>
            <icon src="phpstan.png" height="20"/>&nbsp;PHPStan
        </td>
        <td><code>phpstan/phpdoc-parser</code> 2.3</td>
        <td><code>TypeParser::parse()</code> reads the source whole, leaving no trailing input</td>
    </tr>
    <tr>
        <td>
            <icon src="phan.png" height="20"/>&nbsp;Phan
        </td>
        <td><code>phan/phan</code> 5.5</td>
        <td>
            <code>UnionType::fromStringInContext()</code> returns something other than
            a class name made of the whole source
        </td>
    </tr>
    <tr>
        <td>
            <icon src="phpdocumentor.png" height="20"/>&nbsp;phpDocumentor
        </td>
        <td><code>phpdocumentor/type-resolver</code> 2.0</td>
        <td><code>TypeResolver::resolve()</code> returns a type, the source read whole</td>
    </tr>
</table>

> Two of these tools answer even where they did not understand the question, and
> the rules above are what tells the two apart.
>
> Phan reads whatever it cannot parse as a class name, so a `0b1010` comes back
> as a class called `\0b1010` rather than as an error. phpDocumentor builds on
> `phpstan/phpdoc-parser` and keeps what it managed to read, so a `JSON_*` comes
> back as the `JSON_` in front of it, the mask silently dropped. A verdict here
> counts neither.
> {style="note"}

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
        <td>
            <icon src="phan.png" height="20"/>&nbsp;<a href="https://github.com/phan/phan">Phan</a>
        </td>
        <td>
            <icon src="phpdocumentor.png" height="20"/>&nbsp;<a href="https://github.com/phpDocumentor/TypeResolver">phpDocumentor</a>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">20/20</format>
            </note>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">10/20</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">14/20</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">8/20</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">14/20</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="logical-types.md" anchor="intersection-types">
                Logical intersection types
            </a>
            <code-block lang="typescript">
            T &amp; U &amp; V
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="logical-types.md" anchor="parentheses">
                Grouping parentheses
            </a>
            <code-block lang="typescript">
            (T | U)[]
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="offset-access.md">
                Type offsets (offset access)
            </a>
            <code-block lang="typescript">
            ExampleShape['key']
            ClassName::CONSTANT[0]
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="warning.svg"/> <a anchor="ref-1-8-1">Trailing comma not supported <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. Psalm does NOT support arguments ending with a comma" id="ref-1-8-1">
                    <code-block lang="typescript">
                    ExampleCollection&lt;array-key, User,>
                    </code-block>
                    <a href="https://psalm.dev/r/866c32c49d">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="warning.svg"/> <a anchor="ref-1-9-1">Other keywords <sup>1</sup></a></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-1-9-1">Other keywords <sup>1</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. PHPStan spells call-site variance out in words" id="ref-1-9-1">
                    <p>
                        The <code>in</code> and <code>out</code> hints are not read, but the
                        same thing is said with <code>contravariant</code> and
                        <code>covariant</code>.
                    </p>
                    <code-block lang="typescript">
                    Collection&lt;covariant Animal>
                    </code-block>
                    <a href="https://phpstan.org/blog/whats-up-with-template-covariant#call-site-variance">Open in phpstan.org</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="generic-types.md" anchor="wildcard-arguments">
                Wildcard template arguments
            </a>
            <code-block lang="typescript">
            ExampleCollection&lt;*>
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-1-10-1">Read as a bivariant mixed <sup>1</sup></a></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-1-10-1">Read as a bivariant mixed <sup>1</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. PHPStan keeps no wildcard of its own" id="ref-1-10-1">
                    <p>
                        An asterisk is read as a <code>mixed</code> carrying a
                        <code>bivariant</code> hint, so the argument parses, but nothing
                        of the wildcard itself is left in the tree.
                    </p>
                    <code-block lang="typescript">
                    // Written
                    ExampleCollection&lt;*>
                    </code-block>
                    <code-block lang="typescript">
                    // Read
                    ExampleCollection&lt;bivariant mixed>
                    </code-block>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="basic-types.md" anchor="this-type">
                The <code>$this</code> type
            </a>
            <code-block lang="typescript">
            $this
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="const-types.md" anchor="constant-masks">
                Suffixed class constant mask types
            </a>
            <code-block lang="typescript">
            ClassName::*_SUFFIX
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="const-types.md" anchor="constant-masks">
                Class constant mask types carrying both ends
            </a>
            <code-block lang="typescript">
            ClassName::PREFIX_*_SUFFIX
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="const-types.md" anchor="constant-masks">
                Suffixed global constant mask types
            </a>
            <code-block lang="typescript">
            *_SUFFIX
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="const-types.md" anchor="constant-masks">
                Namespaced constant mask types
            </a>
            <code-block lang="typescript">
            Path\To\JSON_*
            Path\To\*
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="basic-types.md" anchor="comments">
                Comments inside a type
            </a>
            <code-block lang="typescript">
            int /* comment */ | string
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
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
        <td>
            <icon src="phan.png" height="20"/>&nbsp;<a href="https://github.com/phan/phan">Phan</a>
        </td>
        <td>
            <icon src="phpdocumentor.png" height="20"/>&nbsp;<a href="https://github.com/phpDocumentor/TypeResolver">phpDocumentor</a>
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
                <format style="bold" color="RosyBrown">3/6</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">0/6</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">3/6</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="conditional-types.md" anchor="equality-operators">
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
        <td><icon src="ok.svg"/> <a anchor="ref-2-1-1">Brackets required <sup>1</sup></a></td>
        <td><icon src="ok.svg"/> <a anchor="ref-2-1-1">Brackets required <sup>1</sup></a></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/> <a anchor="ref-2-1-1">Brackets required <sup>1</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. Psalm and PHPStan require the condition to be parenthesised" id="ref-2-1-1">
                    <p>
                        A condition is written inside brackets and is only read there, so
                        the bare form below is read as the type <code>T</code> alone.
                    </p>
                    <code-block lang="typescript">
                    (T is A ? B : C)
                    </code-block>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="conditional-types.md" anchor="equality-operators">
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
        <td><icon src="ok.svg"/> <a anchor="ref-2-2-1">Brackets required <sup>1</sup></a></td>
        <td><icon src="ok.svg"/> <a anchor="ref-2-2-1">Brackets required <sup>1</sup></a></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/> <a anchor="ref-2-2-1">Brackets required <sup>1</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. Psalm and PHPStan require the condition to be parenthesised" id="ref-2-2-1">
                    <p>
                        A condition is written inside brackets and is only read there, so
                        the bare form below is read as the type <code>T</code> alone.
                    </p>
                    <code-block lang="typescript">
                    (T is A ? B : C)
                    </code-block>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="conditional-types.md" anchor="equality-operators">
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
        <td><icon src="ok.svg"/> <a anchor="ref-2-3-1">Brackets required <sup>1</sup></a></td>
        <td><icon src="ok.svg"/> <a anchor="ref-2-3-1">Brackets required <sup>1</sup></a></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/> <a anchor="ref-2-3-1">Brackets required <sup>1</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. Psalm and PHPStan require the condition to be parenthesised" id="ref-2-3-1">
                    <p>
                        A condition is written inside brackets and is only read there, so
                        the bare form below is read as the type <code>T</code> alone.
                    </p>
                    <code-block lang="typescript">
                    (T is A ? B : C)
                    </code-block>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="conditional-types.md" anchor="yoda-style-conditions">
                Conditional referenced types
            </a>
            <a href="https://en.wikipedia.org/wiki/Yoda_conditions">in Yoda-style</a>
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
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="conditional-types.md" anchor="equality-operators">
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
        <td><icon src="ok.svg"/> <a anchor="ref-2-5-1">List of supported functions <sup>1</sup></a></td>
        <td><icon src="ko.svg"/> <a anchor="ref-2-5-2">No call syntax <sup>2</sup></a></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/> <a anchor="ref-2-5-2">No call syntax <sup>2</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. Psalm supports the following functions" id="ref-2-5-1">
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
                <def title="2. The PHPStan grammar carries no call of any kind" id="ref-2-5-2">
                    <p>
                        A parenthesis behind a name ends the type, whatever the name is,
                        so the functions PHPStan reads in a condition are read by rules
                        of its own rather than by the type language.
                    </p>
                    <code-block lang="typescript">
                    (func_num_args() is 1 ? A : B)
                    // Unexpected token "(", expected ')' at offset 14
                    </code-block>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="conditional-types.md" anchor="yoda-style-conditions">
                Functions in conditional types
            </a>
            <a href="https://en.wikipedia.org/wiki/Yoda_conditions">in Yoda-style</a>
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
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
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
        <td>
            <icon src="phan.png" height="20"/>&nbsp;<a href="https://github.com/phan/phan">Phan</a>
        </td>
        <td>
            <icon src="phpdocumentor.png" height="20"/>&nbsp;<a href="https://github.com/phpDocumentor/TypeResolver">phpDocumentor</a>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">21/21</format>
            </note>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">7/21</format>
            </warning>
        </td>
        <td>
            <note>
                <format style="bold" color="DarkSeaGreen">21/21</format>
            </note>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">5/21</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">15/21</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="literal-types.md" anchor="integer">
                BigInteger (<code>PHP_INT_MAX + 1</code> or <code>PHP_INT_MIN - 1</code>) literals
            </a>
            <code-block lang="typescript">
            9999999999999999999
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/> <a anchor="ref-3-9-1">Kept in full <sup>1</sup></a></td>
        <td><icon src="warning.svg"/> <a anchor="ref-3-9-2">Works with restrictions <sup>2</sup></a></td>
        <td><icon src="ok.svg"/> <a anchor="ref-3-9-3">Kept in full <sup>3</sup></a></td>
        <td><icon src="warning.svg"/> <a anchor="ref-3-9-4">Works with restrictions <sup>4</sup></a></td>
        <td><icon src="warning.svg"/> <a anchor="ref-3-9-5">Works with restrictions <sup>5</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. TypeLang keeps the number whole, however large it is" id="ref-3-9-1">
                    <p>
                        The <code>$decimal</code> carries the value written out in base
                        10, so a number too large for the platform's <code>int</code> is
                        still readable in full. The <code>$value</code> beside it is the
                        native one, and that is the only one a platform limit applies to.
                    </p>
                    <code-block lang="typescript">
                    // Input
                    9999999999999999999
                    </code-block>
                    <code-block lang="typescript">
                    // Stored
                    TypeLang\Type\Literal\IntLiteralNode {
                      +offset: 0
                      +value: 9223372036854775807
                      +raw: "9999999999999999999"
                      +decimal: "9999999999999999999"
                    }
                    </code-block>
                </def>
                <def title="2. Psalm limits value to min/max int" id="ref-3-9-2">
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
                <def title="3. PHPStan reads the number whole, and loses it afterwards" id="ref-3-9-3">
                    <p>
                        The literal is kept as it was written, so nothing is lost while
                        the type is read. The number does not survive the analysis that
                        follows, though: what comes out of it is the nearest
                        <code>int</code> the platform carries.
                    </p>
                    <code-block lang="typescript">
                    // Input
                    9999999999999999999
                    </code-block>
                    <code-block lang="typescript">
                    // Stored
                    PHPStan\PhpDocParser\Ast\ConstExpr\ConstExprIntegerNode {
                      value: "9999999999999999999"
                    }
                    </code-block>
                    <code-block lang="php">
                    // Analysed
                    \PHPStan\dumpType($value);
                    // Dumped type: 9223372036854775807
                    </code-block>
                    <a href="https://phpstan.org/r/e8f6fef0-7726-412a-9c99-9eef04c53c17">Open in phpstan.org</a>
                </def>
                <def title="4. Phan turns the number into a float" id="ref-3-9-4">
                    <code-block lang="typescript">
                    // Input
                    9999999999999999999
                    </code-block>
                    <code-block lang="typescript">
                    // Stored
                    1.0E+19
                    </code-block>
                </def>
                <def title="5. phpDocumentor limits value to min/max int" id="ref-3-9-5">
                    <code-block lang="typescript">
                    // Input
                    9999999999999999999
                    </code-block>
                    <code-block lang="typescript">
                    // Stored
                    9223372036854775807
                    </code-block>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-3-10-1">Value read as 0 <sup>1</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. phpDocumentor reads the number as 0" id="ref-3-10-1">
                    <p>
                        The number parses, but the value behind it is built with a plain
                        cast, so nothing of the base survives.
                    </p>
                    <code-block lang="typescript">
                    // Written
                    0b10101010
                    </code-block>
                    <code-block lang="typescript">
                    // Read
                    0
                    </code-block>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-3-11-1">Value read as 0 <sup>1</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. phpDocumentor reads the number as 0" id="ref-3-11-1">
                    <p>
                        The number parses, but the value behind it is built with a plain
                        cast, so nothing of the base survives.
                    </p>
                    <code-block lang="typescript">
                    // Written
                    0o42
                    </code-block>
                    <code-block lang="typescript">
                    // Read
                    0
                    </code-block>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-3-12-1">Value read as decimal <sup>1</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. phpDocumentor reads the number as 42" id="ref-3-12-1">
                    <p>
                        The number parses, but the value behind it is built with a plain
                        cast, so nothing of the base survives.
                    </p>
                    <code-block lang="typescript">
                    // Written
                    042
                    </code-block>
                    <code-block lang="typescript">
                    // Read
                    42
                    </code-block>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-3-13-1">Value read as 0 <sup>1</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. phpDocumentor reads the number as 0" id="ref-3-13-1">
                    <p>
                        The number parses, but the value behind it is built with a plain
                        cast, so nothing of the base survives.
                    </p>
                    <code-block lang="typescript">
                    // Written
                    0xDEAD_BEEF
                    </code-block>
                    <code-block lang="typescript">
                    // Read
                    0
                    </code-block>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="literal-types.md" anchor="integer">
                Underscore (<code>_</code>) separators in integer literals
            </a>
            <code-block lang="typescript">
            42_04
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="literal-types.md" anchor="integer">
                Explicitly signed number literals
            </a>
            <code-block lang="typescript">
            +42
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="literal-types.md" anchor="scientific-notation">
                Float literals in scientific notation carrying a signed exponent
            </a>
            <code-block lang="typescript">
            -1.5e+3
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
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="literal-types.md" anchor="hexadecimal">
                Hexadecimal integer literals carrying an <code>e</code>
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-3-21-1">Value read as 0 <sup>1</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. phpDocumentor reads the number as 0" id="ref-3-21-1">
                    <p>
                        The number parses, but the value behind it is built with a plain
                        cast, so nothing of the base survives.
                    </p>
                    <code-block lang="typescript">
                    // Written
                    0x42e2
                    </code-block>
                    <code-block lang="typescript">
                    // Read
                    0
                    </code-block>
                </def>
            </deflist>
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
        <td>
            <icon src="phan.png" height="20"/>&nbsp;<a href="https://github.com/phan/phan">Phan</a>
        </td>
        <td>
            <icon src="phpdocumentor.png" height="20"/>&nbsp;<a href="https://github.com/phpDocumentor/TypeResolver">phpDocumentor</a>
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
                <format style="bold" color="RosyBrown">9/15</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">13/15</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">5/15</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">13/15</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md" anchor="explicit-keys">
                Explicit shape types
            </a>
            <code-block lang="typescript">
            array {
                key: ValueType
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md" anchor="explicit-keys">
                Trailing comma in explicit shape types
            </a>
            <code-block lang="typescript">
            array {
                key: ValueType,
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md" anchor="implicit-keys">
                Implicit shape types
            </a>
            <code-block lang="typescript">
            array {
                ValueType
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/> <a anchor="ref-4-3-1">Arrays and lists only <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/> <a anchor="ref-4-3-1">Arrays and lists only <sup>1</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. PHPStan reads a key-less field in an array or a list alone" id="ref-4-3-1">
                    <code-block lang="typescript">
                    // OK
                    array { ValueType }
                    list { ValueType }
                    </code-block>
                    <code-block lang="typescript">
                    // Syntax Error
                    object { ValueType }
                    </code-block>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md" anchor="implicit-keys">
                Trailing comma in implicit shape types
            </a>
            <code-block lang="typescript">
            array {
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
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md" anchor="optional-fields">
                Optional keys in explicit shape types
            </a>
            <code-block lang="typescript">
            array {
                key?: ValueType
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md">
                Empty (closed) shape types
            </a>
            <code-block lang="typescript">
            array {}
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md" anchor="unsealed-shapes">
                Unsealed shape types
            </a>
            <code-block lang="typescript">
            array { ... }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-4-7-1">Read as an empty array <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. Psalm loses the unsealed part where no field stands in front of it" id="ref-4-7-1">
                    <p>
                        A shape carrying fields keeps what follows them, but one made of
                        the ellipsis alone comes back sealed and empty.
                    </p>
                    <code-block lang="typescript">
                    // Written
                    array { ... }
                    </code-block>
                    <code-block lang="typescript">
                    // Read
                    array&lt;never, never>
                    </code-block>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md" anchor="unsealed-shapes">
                Explicit unsealed shape types
            </a>
            <code-block lang="typescript">
            array {
                key: ValueType,
                ...
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md" anchor="unsealed-shapes">
                Implicit unsealed shape types
            </a>
            <code-block lang="typescript">
            array {
                ValueType,
                ...
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md" anchor="typed-shapes">
                Typed unsealed shape types
            </a>
            <code-block lang="typescript">
            array {
                ...&lt;array-key, ValueType>
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md" anchor="constant-keys">
                Class constant shape keys
            </a>
            <code-block lang="typescript">
            array {
                Path\To\ClassName::CONSTANT_NAME: string,
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md" anchor="constant-mask-keys">
                Constant mask shape keys
            </a>
            <code-block lang="typescript">
            array {
                Path\To\ClassName::PREFIX_*: string,
                JSON_*: string,
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md" anchor="string-keys">
                String literal shape keys
            </a>
            <code-block lang="typescript">
            array {
                'some key': ValueType,
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md" anchor="numeric-keys">
                Numeric shape keys
            </a>
            <code-block lang="typescript">
            array {
                0: ValueType,
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="shape-types.md">
                Shapes of an arbitrary type name
            </a>
            <code-block lang="typescript">
            Custom\ObjectType {
                key: ValueType
            }
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/> <a anchor="ref-4-15-1">Fixed set of names <sup>1</sup></a></td>
        <td><icon src="ko.svg"/> <a anchor="ref-4-15-2">Fixed set of names <sup>2</sup></a></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ko.svg"/> <a anchor="ref-4-15-2">Fixed set of names <sup>2</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. Psalm names the types a shape may be written of" id="ref-4-15-1">
                    <p>
                        A shape follows an <code>array</code>, a <code>list</code>, an
                        <code>object</code> or a <code>callable-array</code>, and no other
                        name.
                    </p>
                    <a href="https://psalm.dev/r/4ec6feecc1">Open in psalm.dev</a>
                </def>
                <def title="2. PHPStan names the types a shape may be written of" id="ref-4-15-2">
                    <p>
                        A shape follows an <code>array</code>, a <code>list</code> or an
                        <code>object</code>, and no other name.
                    </p>
                </def>
            </deflist>
        </td>
    </tr>
</table>

## Callable Types

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
        <td>
            <icon src="phan.png" height="20"/>&nbsp;<a href="https://github.com/phan/phan">Phan</a>
        </td>
        <td>
            <icon src="phpdocumentor.png" height="20"/>&nbsp;<a href="https://github.com/phpDocumentor/TypeResolver">phpDocumentor</a>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">20/21</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">10/21</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">18/21</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">13/21</format>
            </warning>
        </td>
        <td>
            <warning>
                <format style="bold" color="RosyBrown">17/21</format>
            </warning>
        </td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="callable-types.md">
                Trailing comma in the parameter list
            </a>
            <code-block lang="typescript">
            callable(Type,): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="callable-types.md" anchor="optional-parameters">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="callable-types.md" anchor="output-parameters">
                Callable with output parameters
            </a>
            <code-block lang="typescript">
            callable(T&amp;): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-5-8-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. Psalm reads an output parameter as an intersection" id="ref-5-8-1">
                    <code-block lang="typescript">
                    // OK
                    callable(T&amp;): U
                    </code-block>
                    <code-block lang="typescript">
                    // Bug: Intersection types must be all objects,
                    // Psalm\Type\Atomic\TInt provided in docblock
                    callable(int&amp;): U
                    </code-block>
                    <a href="https://psalm.dev/r/9a5a81443f">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="callable-types.md" anchor="output-parameters">
                Callable with output optional parameters
            </a>
            <code-block lang="typescript">
            callable(T&amp;=): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-5-9-1">Works with restrictions <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. Psalm reads an output parameter as an intersection" id="ref-5-9-1">
                    <code-block lang="typescript">
                    // OK
                    callable(T&amp;): U
                    </code-block>
                    <code-block lang="typescript">
                    // Bug: Intersection types must be all objects,
                    // Psalm\Type\Atomic\TInt provided in docblock
                    callable(int&amp;): U
                    </code-block>
                    <a href="https://psalm.dev/r/9a5a81443f">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="callable-types.md" anchor="output-parameters">
                Callable with output optional named parameters
            </a>
            <code-block lang="typescript">
            callable(T &amp;$name=): T
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="callable-types.md" anchor="variadic-parameters">
                Callable with named variadic parameters
            </a>
            <code-block lang="typescript">
            callable(Type ...$name): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="callable-types.md" anchor="variadic-parameters">
                Callable with output named variadic parameters
            </a>
            <code-block lang="typescript">
            callable(Type &amp;...$name): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
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
        <td><icon src="ko.svg"/> <a anchor="ref-5-14-1">Suffix form only <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/a578af59-2099-4244-a297-67b74f9729b5">
                Not Supported
            </a>
        </td>
        <td><icon src="ko.svg"/> <a anchor="ref-5-14-2">Parameter dropped <sup>2</sup></a></td>
        <td><icon src="ko.svg"/></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. TypeLang carries one spelling of a variadic, and it is the suffix" id="ref-5-14-1">
                    <p>
                        The ellipsis stands behind the type, the way PHP itself writes it.
                        The prefix form was read by earlier versions of the parser and is
                        a syntax error now.
                    </p>
                    <code-block lang="typescript">
                    callable(Type...): T
                    </code-block>
                </def>
                <def title="2. Phan drops the parameter rather than reporting it" id="ref-5-14-2">
                    <code-block lang="typescript">
                    // Written
                    callable(...Type): T
                    </code-block>
                    <code-block lang="typescript">
                    // Read
                    callable(): T
                    </code-block>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="callable-types.md" anchor="variadic-parameters">
                Callable with optional variadic parameters
            </a>
            <code-block lang="typescript">
            callable(Type...=): T
            callable(Type ...$name=): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-5-15-1">Error depends on the spelling <sup>1</sup></a></td>
        <td>
            <icon src="ko.svg"/>
            <a href="https://phpstan.org/r/0a7c61e9-691d-414c-983c-b5c32f472214">
                No Error
            </a>
        </td>
        <td><icon src="ko.svg"/> No Error</td>
        <td><icon src="ko.svg"/> No Error</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <tip>
                This expression is incorrect because any variadic parameter is
                already optional. The parser must throw a syntax or semantic
                error.
            </tip>
            <deflist collapsible="true">
                <def title="1. Psalm error depends on parameter definition syntax" id="ref-5-15-1">
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
        <td colspan="6">
            <a href="callable-types.md" anchor="variadic-parameters">
                Callable carrying the variadic marker twice
            </a>
            <code-block lang="typescript">
            callable(...Type ...$name): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="warning.svg"/> <a anchor="ref-5-16-1">Internal error <sup>1</sup></a></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/> No Error</td>
        <td><icon src="ko.svg"/> No Error</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <tip>
                This expression is incorrect because the "variadic" lexeme
                <code>...</code> must be present in the parameter in a single
                copy. The parser must throw a syntax or semantic error.
            </tip>
            <deflist collapsible="true">
                <def title="1. Psalm throws an internal error instead of a valid error message" id="ref-5-16-1">
                    <code-block lang="typescript">
                    callable(...int ...$name)
                    // Internal Psalm error on line ...:
                    // Unrecognised parse tree type Psalm\Internal\Type\ParseTree\CallableParamTree
                    </code-block>
                    <a href="https://psalm.dev/r/d802f62027">Open in psalm.dev</a>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="callable-types.md">
                The <code>$this</code> return type
            </a>
            <code-block lang="typescript">
            callable(): $this
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="callable-types.md" anchor="template-parameters">
                Callable declaring template parameters
            </a>
            <code-block lang="typescript">
            callable&lt;T>(T): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="callable-types.md" anchor="template-parameters">
                Upper bound of a template parameter
            </a>
            <code-block lang="typescript">
            callable&lt;T of Some>(T): T
            callable&lt;T as Some>(T): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/> <a anchor="ref-5-19-1">Both words read as one <sup>1</sup></a></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/> <a anchor="ref-5-19-1">Both words read as one <sup>1</sup></a></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="5">
            <deflist collapsible="true">
                <def title="1. PHPStan keeps no record of which word was written" id="ref-5-19-1">
                    <p>
                        An <code>as</code> comes back as an <code>of</code>, where TypeLang
                        keeps the bound as it was written.
                    </p>
                    <code-block lang="typescript">
                    // Written
                    callable&lt;T as Some>(T): T
                    </code-block>
                    <code-block lang="typescript">
                    // Read
                    callable&lt;T of Some>(T): T
                    </code-block>
                </def>
            </deflist>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="callable-types.md" anchor="template-parameters">
                Lower bound of a template parameter
            </a>
            <code-block lang="typescript">
            callable&lt;T super Some>(T): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
    <tr>
        <td colspan="6">
            <a href="callable-types.md" anchor="template-parameters">
                Default of a template parameter
            </a>
            <code-block lang="typescript">
            callable&lt;T = int>(T): T
            </code-block>
        </td>
    </tr>
    <tr>
        <td></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
        <td><icon src="ko.svg"/></td>
        <td><icon src="ok.svg"/></td>
    </tr>
</table>
