<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\DocBlock\Polyfill\Description;

use TypeLang\DocBlock\Description\DescriptionInterface;
use TypeLang\DocBlock\Tag\TagInterface;
use TypeLang\PHPDoc\DocBlock\Tag\TagsProviderInterface;

/**
 * @internal polyfill interface for the {@see \TypeLang\DocBlock\Description\TaggedDescriptionInterface}
 *
 * @template-extends \Traversable<array-key, DescriptionInterface|TagInterface>
 *
 * @property-read \Traversable<array-key, DescriptionInterface|TagInterface> $components
 */
interface TaggedDescriptionInterface extends
    DescriptionInterface,
    TagsProviderInterface,
    \Traversable,
    \Countable {}
