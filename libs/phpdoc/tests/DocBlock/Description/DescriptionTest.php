<?php

declare(strict_types=1);

namespace TypeLang\PhpDoc\Tests\DocBlock\Tag;

use PHPUnit\Framework\Attributes\Test;
use TypeLang\PhpDoc\Tests\TestCase;
use TypeLang\PhpDoc\DocBlock\Description\Description;
use TypeLang\PhpDoc\DocBlock\Description\DescriptionInterface;

final class DescriptionTest extends TestCase
{
    #[Test]
    public function constructorStoresValue(): void
    {
        $description = new Description('Some text');

        $this->assertSame('Some text', $description->value);
    }

    #[Test]
    public function valueDefaultsToEmptyString(): void
    {
        $this->assertSame('', new Description()->value);
    }

    #[Test]
    public function toStringReturnsValue(): void
    {
        $this->assertSame('Some text', (string) new Description('Some text'));
    }

    #[Test]
    public function implementsDescriptionInterface(): void
    {
        $this->assertInstanceOf(DescriptionInterface::class, new Description());
    }

    #[Test]
    public function createFromStringWrapsScalarString(): void
    {
        $description = Description::createFromString('text');

        $this->assertInstanceOf(DescriptionInterface::class, $description);
        $this->assertSame('text', (string) $description);
    }

    #[Test]
    public function createFromStringWrapsStringable(): void
    {
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return 'from stringable';
            }
        };

        $description = Description::createFromString($stringable);

        $this->assertSame('from stringable', (string) $description);
    }

    #[Test]
    public function createFromStringReturnsSameDescriptionInstance(): void
    {
        $existing = new Description('existing');

        $this->assertSame($existing, Description::createFromString($existing));
    }

    #[Test]
    public function tryCreateFromStringOrNullReturnsNullForNull(): void
    {
        $this->assertNull(Description::tryCreateFromStringOrNull(null));
    }

    #[Test]
    public function tryCreateFromStringOrNullWrapsScalarString(): void
    {
        $description = Description::tryCreateFromStringOrNull('text');

        $this->assertInstanceOf(DescriptionInterface::class, $description);
        $this->assertSame('text', (string) $description);
    }

    #[Test]
    public function tryCreateFromStringOrNullReturnsSameDescriptionInstance(): void
    {
        $existing = new Description('existing');

        $this->assertSame($existing, Description::tryCreateFromStringOrNull($existing));
    }
}
