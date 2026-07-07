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
  [@expected-exception](expected-exception-tag.md), recording the exception
  a test is expected to raise.
* **Formatter control** — [@formatter:off](formatter-off-tag.md) and
  [@formatter:on](formatter-on-tag.md), bracketing a region the reformatter
  should leave untouched.

<warning>
The tags in this group are listed for reference only. They are catalogued so
the vocabulary is documented in one place, but are <b>not yet recognized</b>
by the parser — a docblock containing one currently falls back to a plain
<a href="phpdoc.md#tag">Tag</a>. Their pages are marked work-in-progress in
the sidebar.
</warning>

For the authoritative behavior, see
[PhpStorm's documentation on suppressing inspections](https://www.jetbrains.com/help/phpstorm/disabling-and-enabling-inspections.html)
and its
[language injection guide](https://www.jetbrains.com/help/phpstorm/using-language-injections.html).
