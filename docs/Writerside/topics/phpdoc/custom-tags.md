# Extending

<primary-label ref="phpdoc-component"/>
<show-structure for="chapter" depth="2"/>

None of the tags listed in the sidebar are special-cased inside the parser.
`@param`, `@see`, `@template` and every other tag are declared the exact same
way, and a tag of your own is added through that very same mechanism: a 
**[platform](platforms.md)**.

Adding one takes a few small parts that fit together — a tag object, its
definition, the grammar the definition is written in, the combinators it leans
on, and the platform that registers the lot. This page shows how they connect;
each part is covered in depth on its own page, linked as it comes up and again
under [Where to Go Next](#where-to-go-next).

## Adding a Tag in Practice

Recognizing a `@money` tag of your own — documenting a monetary amount and a
currency — starts from the shape of the line it should accept:

```
"@money" <Amount> <Currency> [ <Description> ]
```

Describing that shape is all the parser needs: it becomes the tag's
**specification**, written with the [`Spec`](grammar.md) grammar DSL — an
integer, then a name, then an optional description:

```php
$spec = Spec::sequence(
    Spec::rule(IntegerCombinator::NAME, 'amount', 'Amount'),
    Spec::rule(NameCombinator::NAME, 'currency', 'Currency'),
    Spec::maybe(
        Spec::rule(DescriptionCombinator::NAME, 'description'),
    ),
);
```

Each `Spec::rule()` takes the combinator that reads that part of the line, the
alias its captured value is stored under, and — optionally — the name (e.g. 
`Amount` or `Currency`) it renders as in the grammar above.

<tip>
The building blocks named here — <code>Integer</code>, <code>Name</code>,
<code>Description</code> — are combinators. See <a href="grammar.md">Grammar</a>
for the <code>Spec</code> DSL and <a href="combinators.md">Combinators</a> for
the blocks it reads with.
</tip>

The value object the parsed line becomes is a small, immutable [tag](tags.md)
class:

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
```

A specification is not used on its own — it is declared inside a
[definition](definitions.md), which pairs it with a `create()` method turning a
match into that tag object:

```php
final class MoneyTagDefinition extends TagDefinition
{
    public const string NAME = 'money';

    public function __construct()
    {
        parent::__construct(self::NAME, Spec::sequence(
            Spec::rule(IntegerCombinator::NAME, 'amount', 'Amount'),
            Spec::rule(NameCombinator::NAME, 'currency', 'currency'),
            Spec::maybe(
                Spec::rule(DescriptionCombinator::NAME, 'description'),
            ),
        ));
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

The aliases declared in the spec — `'amount'`, `'currency'`, `'description'` —
are exactly how `create()` reads the matched pieces back: the `TagPayload` it
receives is keyed by them, so `$result->get('amount')` returns whatever the
`amount` rule captured.

<tip>
<code>get()</code> fetches a required capture, <code>find()</code> an optional
one, and <code>getAll()</code> every value of a repeating one. See
<a href="grammar.md#captures">Captures</a>.
</tip>

Finally, the definition is registered by contributing a [platform](platforms.md)
to the parser:

```php
final class MoneyPlatform implements PlatformInterface
{
    public string $name = 'money';

    public iterable $aliases = [];

    public iterable $combinators = [];

    public iterable $tags {
        get => [
            MoneyTagDefinition::NAME => new MoneyTagDefinition(),
        ];
    }
}

$parser = new DocBlockParser(platforms: [
    new MoneyPlatform(),
]);

$block = $parser->parse('/** @money 100 USD */');
```

A platform's four properties register the three kinds of thing the parser needs:

* **`$tags`** registers the [definitions](definitions.md), keyed by each tag's
  canonical (lower-case) name.
* **`$combinators`** registers any [combinators](combinators.md) the
  definitions reference that are not built in already (`@money` needs none).
* **`$aliases`** maps an alternative spelling to a canonical tag name.

<note>
The built-in <code>StandardPlatform</code> is always loaded first, so every
standard tag stays available; a platform passed in only adds to it, or overrides
an entry when it reuses a name. The other built-in families (phpDocumentor,
Psalm, PHPStan, Phan, PhpStorm, PHP CodeSniffer) are <b>not</b> loaded by the
constructor — list them alongside your own platform when you need them, or see
<a href="platforms.md"><code>DocBlockParser::createDefault()</code></a> for the
full built-in set.
</note>

## Where to Go Next

<deflist>
    <def title="Tags">
        What a parsed tag object is. See <a href="tags.md">Tags</a>.
    </def>
    <def title="Definitions">
        How a tag's parsing and construction are declared. See
        <a href="definitions.md">Definitions</a>.
    </def>
    <def title="Grammar">
        The <code>Spec</code> DSL a definition's body is written in. See
        <a href="grammar.md">Grammar</a>.
    </def>
    <def title="Combinators">
        The building blocks a grammar reads with. See
        <a href="combinators.md">Combinators</a>.
    </def>
    <def title="Platforms">
        Bundling and registering it all. See <a href="platforms.md">Platforms</a>.
    </def>
</deflist>
