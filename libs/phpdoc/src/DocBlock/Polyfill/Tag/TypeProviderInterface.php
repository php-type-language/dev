<?php

declare(strict_types=1);

namespace TypeLang\PHPDoc\DocBlock\Polyfill\Tag;

use TypeLang\PHPDoc\DocBlock\Tag\OptionalTypeProviderInterface;
use TypeLang\Type\TypeNode;

/**
 * @internal polyfill interface for the {@see \TypeLang\PHPDoc\DocBlock\Tag\TypeProviderInterface}
 *
 * @property-read TypeNode $type
 */
interface TypeProviderInterface extends
    OptionalTypeProviderInterface {}
