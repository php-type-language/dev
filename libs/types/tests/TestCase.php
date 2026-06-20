<?php

declare(strict_types=1);

namespace TypeLang\Node\Tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;

#[Group('unit'), Group('type-lang/types')]
abstract class TestCase extends BaseTestCase {}
