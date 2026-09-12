<?php

declare(strict_types=1);

namespace TypeLang\Type\Literal;

/**
 * @template TValue of scalar = scalar
 *
 * @template-extends LiteralNode<TValue>
 */
abstract class ScalarNode extends LiteralNode {}
