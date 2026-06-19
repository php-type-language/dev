<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\Unit;

use PHPUnit\Framework\Attributes\Group;
use TypeLang\Printer\Tests\TestCase as BaseTestCase;

#[Group('unit'), Group('type-lang/printer')]
abstract class TestCase extends BaseTestCase {}
