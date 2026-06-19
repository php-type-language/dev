# Literal Types

<show-structure for="chapter" depth="2"/>

As a type description, specific values corresponding to a specific type are
allowed:

- `bool`
- `string`
- `int`
- `float`
- `null`.

## Boolean And Null

The literals `true` and `false` are used as the value of the PHP `bool` type.

> Note: `true` and `false` values are not case sensitive.
> ```typescript
> TruE
> ```

For PHP `null` types, the `null` literal is used.

> Note: `null` value is not case sensitive.
> ```typescript
> NulL
> ```

For other types the logic is more complicated.

## Strings

The syntax of string types is similar to that in PHP: The string allows single
(`'`) and double (`"`) quotes, which can be escaped using a backslash (`\`).

> Single-quoted string wrapped by `'` symbol.
> ```typescript
> 'I am single-quoted string wrapped by \' symbol'
> ```

> Double-quoted string wrapped by `"` symbol.
> ```typescript
> "I am double-quoted string wrapped by \" symbol"'
> ```

Strings in single quotes are processed "as is", strings in double quotes allow
insertion of special sequences.

### Escape Sequences

All control sequences are similar to the PHP language: 
[https://www.php.net/manual/en/language.types.string.php](https://www.php.net/manual/en/language.types.string.php)

* `\n` — Line break (`0x0A` code)
* `\r` — Carriage return (`0x0D` code)
* `\t` — Tab (`0x09` code)
* `\v` — Vertical Tab (`0x0B` code)
* `\e` — Escape (`0x1B` code)
* `\f` — Form Feed (`0x0C` code)
* `\$` — Dollar Sign (`0x24` code)

> Double-quoted string with new line (`0x0A`) escape sequence.
> ```typescript
> "String with\nNew Line"
> ```
> ```
> String with
> New Line
> ```

> Single-quoted string with ignored escape sequence.
> ```typescript
> 'String without\nNew Line'
> ```
> ```
> String without\nNew Line
> ```

### Hexadecimal Sequences

The sequence of characters matching the regular expression `[0-9A-Fa-f]{1,2}` 
is a character in hexadecimal notation (e.g. `"\x41" === "A"`).

> `"Hello World"` string equivalent in hexadecimal sequences format.
> ```typescript
> "\x48\x65\x6c\x6c\x6f\x20\x57\x6f\x72\x6c\x64"
> ```
> ```
> Hello World
> ```

> Single-quoted string does not process such sequences.
> ```typescript
> '\x48\x65\x6c\x6c\x6f\x20\x57\x6f\x72\x6c\x64'
> ```
> ```
> \x48\x65\x6c\x6c\x6f\x20\x57\x6f\x72\x6c\x64
> ```

### Unicode Sequences

the sequence of characters matching the regular expression `[0-9A-Fa-f]+` is a
Unicode codepoint, which will be output to the string as that codepoint's UTF-8
representation. The braces are required in the sequence. E.g. `"\u{41}" === "A"`

> [`"😊"` unciode char](https://www.compart.com/en/unicode/U+1F60A) equivalent.
> ```typescript
> "\u{1F60A}"
> ```
> ```
> 😊
> ```

> Single-quoted string does not process such sequences.
> ```typescript
> '\u{1F60A}'
> ```
> ```
> \u{1F60A}
> ```

## Integer

The syntax of integer numbers is similar to the PHP language. Binary, octal,
decimal and hexadecimal number systems are supported.

> Please note that in addition to numbers, underscores (`_`) are allowed.
> {style="note"}

Decimal numbers can contain any digits from `0` to `9` (leading `0` not allowed)
and must match the regular expression `[1-9][0-9]*`. For any other numbers,
a special prefixed format is used, described below. Negative values are prefixed
with a minus (`-`).

### Binary

Every binary number is prefixed with `0b` or `0B` and can only contain the
numbers `1` and `0` and must match the regular expression `0b[0-1]+`.

<tabs>
<tab title="Examples">

> Number `173` in binary format.
> ```typescript
> 0b10101101
> ```

> Also number `173` in binary format with `_` delimiters.
> ```typescript
> 0b10_10_11_01
> ```

> Number `10` in binary format: 
> - The `0b` or `0B` prefix can be in any case.
> ```typescript
> 0B1010
> ```

> Negative number `10` in binary format.
> ```typescript
> -0b1010
> ```

</tab>
<tab title="Counterexamples">

> Binary numbers can only contain `1` and `0` digits.
> ```typescript
> 0b101042
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "42"
> ```
> {style="warning"}

</tab>
</tabs>

### Octal

Every octal number is prefixed with `0o` or `0O` and can only contain the
numbers between `0` and `7` and must match the regular expression
`0o[0-7]+` or `[0-7]+`.

> For compatibility with older versions of PHP, the prefix `0` is also allowed.
{style="note"}

<tabs>
<tab title="Examples">

> Number `34` in octal format.
> ```typescript
> 0o42
> ```

> Also number `34` in octal format: 
> - The `0o` or `0O` prefix can be in any case.
> ```typescript
> 0O42
> ```

> Also number `34` in "legacy" octal format.
> ```typescript
> 042
> ```

> Number `2195` in octal format with `_` delimiters.
> ```typescript
> 0o42_23
> ```

</tab>
<tab title="Counterexamples">

> Octal numbers can only contain digits between `0` and `7`.
> ```typescript
> 0o4281
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "81"
> ```
> {style="warning"}

</tab>
</tabs>

### Hexadecimal

Every hexadecimal number is prefixed with `0x` or `0X` and can only contain
the numbers between `0` and `F` and must match the regular expression 
`0x[0-9a-fA-F]+`.

<tabs>
<tab title="Examples">

> Number `57005` in hexadecimal format.
> ```typescript
> 0xDEAD
> ```

> Also number `57005` in hexadecimal format: 
> - The `0x` or `0X` prefix can be in any case.
> - The `A-F` letters can also be in any case.
> ```typescript
> 0XDeaD
> ```

> Number `3735928559` in hexadecimal format with `_` delimiters.
> ```typescript
> 0xDEAD_BEEF
> ```

</tab>
<tab title="Counterexamples">

> Hexadecimal numbers can only contain digits between `0` and `F`.
> ```typescript
> 0xHELL
> ```
> {style="warning"}

</tab>
</tabs>

## Float

The syntax of float numbers is similar to the PHP language. Basic floating point
syntax and scientific notation are supported.

Every floating point number accepts the format `[0-9]+\.[0-9]+`. The leading or
trailing number may be omitted. Negative values are prefixed with a minus (`-`.

<tabs>
<tab title="Examples">

> Simple floating point literal value.
> ```typescript
> 0.9
> ```

> Non-prefixed floating point literal value (equivalent of `0.9`).
> ```typescript
> .9
> ```

> Non-suffixed floating point literal value (equivalent of `1.0`).
> ```typescript
> 1.
> ```

> Negative floating point literal value.
> ```typescript
> -0.9
> ```

</tab>
<tab title="Counterexamples">

> Only leading or trailing number may be omitted (not both).
> ```typescript
> .
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "."
> ```
> {style="warning"}

> Only number between `0` and `9` are allowed.
> ```typescript
> 0.0A
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "A"
> ```
> {style="warning"}

</tab>
</tabs>

### Scientific Notation


Scientific notation is a way of expressing numbers that are too large or too
small to be conveniently written in decimal form, since to do so would require
writing out an inconveniently long string of digits.

<tabs>
<tab title="Examples">

> Number `1000.0` in scientific notation.
> ```typescript
> 10e2
> ```

> Also number `1000.0` in scientific notation:
> - The `e` or `E` suffix can be in any case.
> ```typescript
> 10E2
> ```

> Number `0.1` in scientific notation (negative exponent).
> ```typescript
> 10e-2
> ```

</tab>
<tab title="Counterexamples">

> The exponent value must be in decimal (digits between `0` and `9`) form only.
> ```typescript
> 10e-F
> ```
>
> An error similar to the one below should occur
> ```
> ParseException: Syntax error, unexpected "e-F"
> ```
> {style="warning"}

</tab>
</tabs>