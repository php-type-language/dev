<?php

declare(strict_types=1);

namespace TypeLang\Bench\Mapper\Stub;

use TypeLang\Mapper\Mapping\DiscriminatorMap;

#[DiscriminatorMap(field: 'type', map: [
    'circle' => CircleStub::class,
    'square' => SquareStub::class,
])]
abstract class ShapeStub {}
