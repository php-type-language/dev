<?php

declare(strict_types=1);

namespace TypeLang\Printer\Tests\Functional;

use PHPUnit\Framework\Attributes\Group;
use TypeLang\Printer\Tests\TestCase as BaseTestCase;

#[Group('functional'), Group('type-lang/printer')]
abstract class TestCase extends BaseTestCase {}
