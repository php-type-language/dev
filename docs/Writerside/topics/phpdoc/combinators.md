# Combinators

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

Every named building block a [tag's grammar](grammar.md) is assembled
from — a type, a variable, a URI, an email address — is a **combinator**:
a small object that knows how to read exactly one piece of syntax and
nothing else. `Spec::rule(TypeCombinator::NAME, 'type')` in
[`@param`](param-tag.md)'s definition, for instance, refers to the
combinator below by name.

```php
/**
 * @template-covariant TResult of mixed = mixed
 */
interface CombinatorInterface
{
    /**
     * @return TResult
     */
    public function __invoke(Cursor $cursor): mixed;
}
```

Given a `Cursor` positioned at the start of whatever text is still left to
parse, a combinator either consumes the part that belongs to it and returns
the value it read, or throws `NoMatchException` and leaves the cursor
exactly where it found it. That second case matters: a combinator that
partially consumes input before deciding it does not fit would corrupt
whichever alternative the surrounding grammar tries next — see
[`Spec::oneOf()`](grammar.md), which relies on being able to roll back
cleanly.

The `Cursor` itself offers the handful of reading operations most
combinators need, so a combinator rarely has to touch its raw position by
hand: `peek()` and `read()` for a fixed number of bytes, `readWhile()` /
`readUntil()` for a run of (or up to) a set of characters, `readWord()` for
the next whitespace-delimited word, `readPhpIdentifier()` /
`readPhpQualifiedName()` for a name (with or without namespace separators),
`readLiteral()` for an exact piece of text, and `readRemainder()` for
whatever is left entirely.

## Built-in Combinators

Fourteen combinators cover every built-in tag's grammar between them:

<table style="both">
    <tr>
        <td width="140">Name</td>
        <td width="160">Returns</td>
        <td>Reads</td>
    </tr>
    <tr>
        <td><code>Visibility</code></td>
        <td><code>Visibility</code></td>
        <td>Method or property visibility.</td>
    </tr>
    <tr>
        <td><code>AuthorName</code></td>
        <td><code>string</code></td>
        <td>Everything up to an optional <code>"&lt;"</code>.</td>
    </tr>
    <tr>
        <td><code>CallableType</code></td>
        <td><code>TypeReference</code></td>
        <td>A <a href="callable-types.md">TypeLang callable type</a>.</td>
    </tr>
    <tr>
        <td><code>Description</code></td>
        <td><code>DescriptionInterface</code></td>
        <td>Everything left, recursively parsed for inline tags.</td>
    </tr>
    <tr>
        <td><code>Email</code></td>
        <td><code>string</code></td>
        <td>An address, up to its closing <code>">"</code>.</td>
    </tr>
    <tr>
        <td><code>Integer</code></td>
        <td><code>int</code></td>
        <td>A non-negative integer.</td>
    </tr>
    <tr>
        <td><code>IssueName</code></td>
        <td><code>string</code></td>
        <td>Letters, digits, <code>_</code>, <code>-</code> and <code>.</code>.</td>
    </tr>
    <tr>
        <td><code>Name</code></td>
        <td><code>string</code></td>
        <td>A single identifier.</td>
    </tr>
    <tr>
        <td><code>Reference</code></td>
        <td><code>CodeReference</code></td>
        <td>A class, function, method, constant, property or variable.</td>
    </tr>
    <tr>
        <td><code>Type</code></td>
        <td><code>TypeReference</code></td>
        <td>A full <a href="introduction.md">TypeLang type</a>.</td>
    </tr>
    <tr>
        <td><code>URI</code></td>
        <td><code>UriReference</code></td>
        <td>A well-formed word, per RFC 3986.</td>
    </tr>
    <tr>
        <td><code>URL</code></td>
        <td><code>UrlReference</code></td>
        <td>A word that additionally carries a scheme.</td>
    </tr>
    <tr>
        <td><code>Variable</code></td>
        <td><code>string</code></td>
        <td>A <code>$name</code>, without the leading <code>$</code>.</td>
    </tr>
    <tr>
        <td><code>Version</code></td>
        <td><code>string</code></td>
        <td>A word beginning with a digit; <br />anything else is left for the description.</td>
    </tr>
</table>

## Writing One of Your Own

A combinator that reads one of a fixed set of keywords looks much like the
built-in `Access` combinator above. This one reads a `low` / `medium` /
`high` priority level:

```php
enum Priority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}

final readonly class PriorityCombinator implements CombinatorInterface
{
    public const string NAME = 'Priority';

    public function __invoke(Cursor $cursor): Priority
    {
        $priority = Priority::tryFrom($cursor->readWord());

        if ($priority === null) {
            throw new NoMatchException('Expected a priority level');
        }

        return $priority;
    }
}
```

Three habits keep a combinator well-behaved inside a larger grammar:

* **Fail before consuming.** Throw `NoMatchException` the moment the input
  is found not to fit, before reading anything that would belong to what
  comes next — a combinator that consumes first and validates afterward
  leaves the cursor somewhere a sibling alternative can no longer make sense
  of.
* **Consume only your own syntax.** Whitespace meant to separate one grammar
  element from the next is handled by `Spec::sequence()`, not by the
  combinator itself; reading past it leaves nothing for the rest of the
  grammar to match.
* **Fail rather than guess.** When more than one reading of the same input
  would be plausible, throwing and letting a different alternative in a
  surrounding `Spec::oneOf()` take over is safer than silently picking one.

Once written, a combinator with a `NAME` constant is used from a tag's
`$spec` exactly like a built-in one — see [Grammar](grammar.md). To make it
available to the parser, register it on a [platform](platforms.md).
