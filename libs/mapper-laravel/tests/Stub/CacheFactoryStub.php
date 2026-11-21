<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel\Tests\Stub;

use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Cache\Repository;

/**
 * Array stores by the name, `array` is the default store.
 */
final class CacheFactoryStub implements Factory
{
    /**
     * @var array<non-empty-string, Repository>
     */
    public array $stores = [];

    public function store($name = null): Repository
    {
        $name ??= 'array';

        return $this->stores[$name] ??= new CacheRepository(new ArrayStore());
    }
}
