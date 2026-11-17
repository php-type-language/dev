<?php

declare(strict_types=1);

namespace TypeLang\Bench\Mapper\Stub;

final class UserStub
{
    public int $id;

    /**
     * @var non-empty-string
     */
    public string $name;

    public string $email;

    public bool $isActive;

    public float $rating;

    public AddressStub $address;
}
