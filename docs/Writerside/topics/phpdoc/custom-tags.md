# Custom Tags

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

None of the tags listed in the sidebar are special-cased inside the
parser. `@param`, `@see`, `@template` and every other tag are all
declared the exact same way: as a small grammar of their own, describing the
part of the line that follows `@tagname`, together with a rule for turning
what that grammar matches into a concrete tag object. This page walks
through that declaration, and the current, pre-public-API way to add a tag
of your own.

## Declaring a Tag's Grammar

A tag definition is a `TagDefinitionInterface` implementation — in practice,
almost always written by extending the `TagDefinition` base class, which
takes care of the boilerplate and leaves only one method to fill in:
`create()`, which turns a match into the concrete tag object.

The grammar itself is written with a handful of factory methods on `Spec`,
combined the same way a regular-expression or parser-combinator library
would be: a sequence of parts, one of several alternatives, an optional
group, a repeated group.

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
Spec::repeat($rule, $min = 0)
```

`Spec::rule()` is how a tag's grammar reaches into
[the type parser, a variable, a URI, and so on](combinators.md) without
knowing anything about how any of those are actually read — that part is
delegated entirely to a **combinator**.

Take the [`@param`](param-tag.md) tag, whose grammar is

```
"@param" <Type> <Variable> [ <Description> ]
```

Written with `Spec`, a type, then a variable, then an optional description,
this becomes:

```php
final class ParamTagDefinition extends TagDefinition
{
    public const string NAME = 'param';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            spec: Spec::sequence(
                Spec::rule(TypeCombinator::NAME, 'type'),
                Spec::rule(
                    VariableCombinator::NAME,
                    'variable',
                ),
                Spec::maybe(
                    Spec::rule(
                        DescriptionCombinator::NAME,
                        'description',
                    ),
                ),
            ),
        );
    }

    public function create(string $name, TagPayload $result): ParamTag
    {
        return new ParamTag(
            name: $name,
            statement: $result->get('type'),
            variable: $result->get('variable'),
            description: $result->find('description'),
        );
    }
}
```

The second argument to `Spec::rule()` — `'type'`, `'variable'`,
`'description'` above — is an **alias**: the key under which whatever that
part of the grammar matched is later retrieved from the `TagPayload` handed
to `create()`. `$result->get('type')` fetches a required part (it throws if
nothing was captured under that alias); `$result->find('description')`
fetches an optional one, wrapped in `Spec::maybe()`, returning `null` when it
did not match. A third method, `$result->getAll()`, returns every value
captured under an alias that can occur more than once — the
[`@suppress`](suppress-tag.md) tag uses exactly that, for its
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

A rule that only shapes the grammar, without producing a value worth keeping
— a literal delimiter such as the `<`/`>` around an email address in
[`@author`](author-tag.md) — is simply written without an alias at all;
it still has to match, but nothing is captured for it.

A tag body that does not match its `$spec` at all never reaches `create()`:
it becomes an [`InvalidTag`](phpdoc.md#usage) before `create()` would even be
called, so a definition never has to guard against a malformed match itself.

## Inline Tags

A definition that passes `isInline: true` to its parent constructor may be
used, in addition to standing on its own line, nested inside a description
as a `{@tag ...}` sequence with balanced braces — parsing it there produces
the exact same kind of tag object either way. Only four built-in tags do:
[`@inheritdoc`](inheritdoc-tag.md), [`@internal`](internal-tag.md),
[`@link`](link-tag.md) and [`@see`](see-tag.md). Every other tag,
`@param` included, is never lifted out of running text: a `{@param}` written
inside a sentence stays exactly as written, as plain text, rather than being
parsed.

## Writing a Tag of Your Own

Recognizing a tag of your own takes the same two pieces every built-in tag
is made of: a small class to hold the parsed result, and a `TagDefinition` to
produce it. A hypothetical `@money` tag, documenting a monetary amount and a
currency, might look like this:

```php
final class MoneyTag extends Tag
{
    public function __construct(
        string $name,
        public readonly int $amount,
        public readonly string $currency,
        ?DescriptionInterface $description = null,
    ) {
        parent::__construct($name, $description);
    }
}

final class MoneyTagDefinition extends TagDefinition
{
    public const string NAME = 'money';

    public function __construct()
    {
        parent::__construct(
            name: self::NAME,
            spec: Spec::sequence(
                Spec::rule(IntegerCombinator::NAME, 'amount'),
                Spec::rule(NameCombinator::NAME, 'currency'),
                Spec::maybe(
                    Spec::rule(
                        DescriptionCombinator::NAME,
                        'description',
                    ),
                ),
            ),
        );
    }

    public function create(string $name, TagPayload $result): MoneyTag
    {
        return new MoneyTag(
            name: $name,
            amount: $result->get('amount'),
            currency: $result->get('currency'),
            description: $result->find('description'),
        );
    }
}
```

<warning>

**There is no public way to register it yet.** `DocBlockParser`'s
constructor takes no arguments, and the `TagFactory` it builds internally has
no method to add a definition after the fact — so there is currently no way
to hand `MoneyTagDefinition` above to an existing `DocBlockParser` instance.
A public registration API (constructor arguments accepting extra
definitions, aliases and combinators) is planned but not yet implemented;
see the `@todo` on `DocBlockParser` itself.

Until it lands, recognizing a custom tag means assembling the same pipeline
`DocBlockParser` builds internally, by hand, with the custom definition
mixed in alongside whichever built-in ones are still needed:

```php
$factory = new TagFactory(
    definitions: [
        MoneyTagDefinition::NAME => new MoneyTagDefinition(),
        // ...every other definition still wanted, built by hand.
    ],
    combinators: [
        IntegerCombinator::NAME => new IntegerCombinator(),
        NameCombinator::NAME => new NameCombinator(),
        // ...every other combinator those definitions reference.
    ],
);

$tagParser = new StringTagParser($factory);
```

This is verbose because nothing from `DocBlockParser`'s own default list is
exposed for reuse — it lives in private methods and has to be rebuilt from
scratch for every definition kept around it.

</warning>
