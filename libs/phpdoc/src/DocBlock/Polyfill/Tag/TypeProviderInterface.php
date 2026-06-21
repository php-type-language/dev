<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\DocBlock\Polyfill\Tag;

use TypeLang\Node\Type\TypeNode;
use TypeLang\PHPDoc\DocBlock\Tag\OptionalTypeProviderInterface;

/**
 * @internal polyfill interface for the {@see \TypeLang\PHPDoc\DocBlock\Tag\TypeProviderInterface}
 *
 * @property-read TypeNode $type
 */
interface TypeProviderInterface extends
    OptionalTypeProviderInterface {}
