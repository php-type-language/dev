# PhpStorm Tags

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

The **PhpStorm Tags** are the annotations that
[PhpStorm](https://www.jetbrains.com/phpstorm/) understands as directives to
the IDE itself rather than to a command-line analyzer. They do not describe
types or generate documentation; they change how the editor treats the
surrounding code — suppressing an inspection, injecting a language into a
string, marking a call as one that is expected to throw.

Because they steer editor behavior, these tags have no vendor prefix in the
`@psalm-*` sense: they are spelled as ordinary tags (`@noinspection`,
`@language`) that happen to be meaningful only inside JetBrains tooling.

## What This Group Covers

* **Inspection control** — [@noinspection](noinspection-tag.md), which
  silences one or more named IDE inspections for the element that follows.
* **Language injection** — [@language](language-tag.md), telling the editor
  that a string or heredoc contains another language (SQL, JSON, regexp)
  so it can be highlighted and analyzed accordingly.
* **Test expectations** —
  [@expectedException](expected-exception-tag.md), recording the exception
  a test is expected to raise.
* **Formatter control** — [@formatter:off](formatter-off-tag.md) and
  [@formatter:on](formatter-on-tag.md), bracketing a region the reformatter
  should leave untouched.

<note>
Every tag in this group is recognized once the <b>PhpStorm</b> platform is
enabled. It ships with the library as <code>PhpStormPlatform</code> and is
loaded by the
<a href="platforms.md"><code>DocBlockParser::createDefault()</code></a>
factory:

<code-block lang="php">
$parser = DocBlockParser::createDefault();
</code-block>

Alternatively, pass <code>new PhpStormPlatform()</code> to the parser
constructor to add it on top of the standard tags. A bare
<code>new DocBlockParser()</code> loads the standard platform only, so an
unrecognized <code>@noinspection</code> or <code>@language</code> tag then
falls back to a plain <a href="phpdoc.md#tag">Tag</a>.
</note>

<note>
<a href="expected-exception-tag.md">@expectedException</a> is not a PhpStorm
directive in the strict sense: it is an old PHPUnit convention that the IDE
happens to understand, and is registered by the same platform.
</note>

For the authoritative behavior, see
[PhpStorm's documentation on suppressing inspections](https://www.jetbrains.com/help/phpstorm/disabling-and-enabling-inspections.html)
and its
[language injection guide](https://www.jetbrains.com/help/phpstorm/using-language-injections.html).
