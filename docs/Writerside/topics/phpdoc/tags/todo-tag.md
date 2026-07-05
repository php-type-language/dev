# @todo

<primary-label ref="phpdoc-component"/>

The `@todo` tag records an unfinished piece of work related to the element
it annotates, so that it can be tracked and picked up later instead of being
forgotten as a stray code comment. Documentation generators and IDEs can
collect every `@todo` across a codebase into a single worklist.

```
"@todo" [ <Description> ]
```

> Describing the outstanding work directly.
> ```php
> /**
>  * @todo Support cancellation once the transport exposes it.
>  */
> public function send(Message $message): bool
> ```

> Referencing a tracking issue alongside the description.
> ```php
> /**
>  * @todo Validate recipient addresses before dispatch (see
>  *       ISSUE-482).
>  */
> ```

Parsing a `@todo` tag produces a `TodoTag` instance. Being a pure marker, it
adds nothing beyond the `$name` and optional `$description` every
[Tag](phpdoc.md#tag) already carries — in practice the description is what
gives the tag its value, since a bare `@todo` says nothing about what
remains to be done.

```php
final class TodoTag extends FlagTag {}
```

Defined by the PSR-19 draft
([FIG proposed PHPDoc Tags, §5.16](https://github.com/php-fig/fig-standards/blob/master/proposed/phpdoc-tags.md#516-todo))
and by
[phpDocumentor](https://docs.phpdoc.org/guide/references/phpdoc/tags/todo.html).
