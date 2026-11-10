# PHP CodeSniffer Tags

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

The **PHP CodeSniffer Tags** are the annotations
[PHP_CodeSniffer](https://github.com/PHPCSStandards/PHP_CodeSniffer)
recognizes to turn its own rules on and off inside a file. Unlike every other
group in this section, these tags have nothing to do with types or
documentation at all — they are coding-standard directives, telling the
sniffer to skip a line, a block, or a whole file when checking style.

They are the tag-based forms that PHP_CodeSniffer used before it moved to its
`phpcs:` comment directives. Only the tag spellings are parsed here: the
`@codingStandardsIgnore*` family and `@phpcsSuppress`.

## What This Group Covers

* **Scoped suppression** — [@phpcsSuppress](phpcs-suppress-tag.md),
  disabling a named sniff for the element it decorates.
* **Region suppression** —
  [@codingStandardsIgnoreStart](coding-standards-ignore-start-tag.md) and
  [@codingStandardsIgnoreEnd](coding-standards-ignore-end-tag.md) bracket a
  block the sniffer should skip, while
  [@codingStandardsIgnoreLine](coding-standards-ignore-line-tag.md) skips a
  single line and
  [@codingStandardsIgnoreFile](coding-standards-ignore-file-tag.md) exempts
  the entire file.
* **General marker** —
  [@codingStandards](coding-standards-tag.md), the bare root of that
  family.

<note>
Every tag in this group is recognized once the <b>PHP CodeSniffer</b> platform
is enabled. It ships with the library as <code>PhpCodeSnifferPlatform</code> and
is loaded by the
<a href="platforms.md"><code>DocBlockParser::createDefault()</code></a> factory:

<code-block lang="php">
$parser = DocBlockParser::createDefault();
</code-block>

Alternatively, pass <code>new PhpCodeSnifferPlatform()</code> to the parser
constructor to add it on top of the standard tags. A bare
<code>new DocBlockParser()</code> loads the standard platform only, so an
unrecognized <code>@phpcsSuppress</code> or <code>@codingStandards*</code> tag then
falls back to a plain <a href="phpdoc.md#tag">Tag</a>.
</note>

For the authoritative behavior, see
[PHP_CodeSniffer's wiki on ignoring code](https://github.com/PHPCSStandards/PHP_CodeSniffer/wiki/Advanced-Usage#ignoring-parts-of-a-file).
