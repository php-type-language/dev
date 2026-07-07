# Grammar

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

A [definition](definitions.md)'s `spec` — the shape of the line that follows
`@tagname` — is written with a handful of factory methods on `Spec`, combined
the same way a regular-expression or parser-combinator library would be: a
sequence of parts, one of several alternatives, an optional group, a repeated
group.

```php
// an exact word or symbol, e.g. 'static'
Spec::literal(string $value)

// a named building block — see Combinators
Spec::rule(string $combinator)

// every rule, one after another
Spec::sequence(...$rules)

// the first rule that applies
Spec::oneOf(...$rules)

// the rule, or nothing at all
Spec::maybe($rule)

// the rule, as many times as it applies
Spec::repeat($rule, $times = 0)
```

`Spec::rule()` is how a grammar reaches into the type parser, a variable, a URI,
and so on without knowing anything about how any of those are actually read —
that part is delegated entirely to a **combinator**.

<tip>
Every <code>Spec::rule()</code> names the combinator that reads that part of the
line. See <a href="combinators.md">Combinators</a> for the built-in set and how
to write one of your own.
</tip>

Take the [`@param`](param-tag.md) tag, whose grammar is

```
"@param" <Type> <Variable> [ <Description> ]
```

Written with `Spec` — a type, then a variable, then an optional description —
this becomes:

```php
Spec::sequence(
    Spec::rule(TypeCombinator::NAME, 'type'),
    Spec::rule(VariableCombinator::NAME, 'variable'),
    Spec::maybe(
        Spec::rule(DescriptionCombinator::NAME, 'description'),
    ),
)
```

## Captures

The second argument to `Spec::rule()` — `'type'`, `'variable'`, `'description'`
above — is an **alias**: the key under which whatever that part of the grammar
matched is later retrieved from the `TagPayload` handed to a definition's
`create()`.

```php
$result->get('type');          // required — throws if nothing was captured
$result->find('description');  // optional — null when it did not match
$result->getAll('issue');      // every value captured under a repeating alias
```

`getAll()` returns every value captured under an alias that can occur more than
once — the [`@suppress`](suppress-tag.md) tag uses exactly that, for its
comma-separated list of issue names:

```
"@suppress" <IssueName> { "," <IssueName> } [ <Description> ]
```

```php
spec: Spec::sequence(
    Spec::rule(IssueNameCombinator::NAME, 'issue'),
    Spec::repeat(
        Spec::sequence(
            Spec::literal(','),
            Spec::rule(IssueNameCombinator::NAME, 'issue'),
        ),
    ),
    Spec::maybe(
        Spec::rule(DescriptionCombinator::NAME, 'description'),
    ),
),
```

```php
// every comma-separated identifier
$issues = $result->getAll('issue');
```

A rule that only shapes the grammar, without producing a value worth keeping —
a literal delimiter such as the `<`/`>` around an email address in
[`@author`](author-tag.md) — is simply written without an alias at all; it
still has to match, but nothing is captured for it.

<note>
A definition reads these captures back inside its <code>create()</code> method.
See <a href="definitions.md#building-the-tag">Definitions</a>.
</note>

## Where to Go Next

<deflist>
    <def title="Combinators">
        The named building blocks a <code>Spec::rule()</code> runs. See
        <a href="combinators.md">Combinators</a>.
    </def>
    <def title="Definitions">
        Where a <code>spec</code> lives and how its captures are turned into a
        tag. See <a href="definitions.md">Definitions</a>.
    </def>
</deflist>
