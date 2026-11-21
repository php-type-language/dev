<?php

declare(strict_types=1);

namespace TypeLang\Mapper\Bridge\Laravel\Tests;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase as BaseTestCase;
use TypeLang\Mapper\Bridge\Laravel\Tests\Stub\CacheFactoryStub;
use TypeLang\Mapper\Bridge\Laravel\TypeLangMapperServiceProvider;

#[Group('unit'), Group('type-lang/mapper-laravel')]
abstract class TestCase extends BaseTestCase
{
    /**
     * Creates a container with the provider registered from the config, the
     * callback registers the services of the test before the provider.
     *
     * @param array<array-key, mixed> $config the `type-lang-mapper` config
     * @param \Closure(Container): void|null $configure
     */
    protected static function createContainer(
        array $config = [],
        bool $isDebug = false,
        ?\Closure $configure = null,
    ): Container {
        $container = new Container();

        $container->instance('config', new Repository([
            'app' => ['debug' => $isDebug],
            TypeLangMapperServiceProvider::CONFIG_KEY => $config,
        ]));

        $container->instance('cache', new CacheFactoryStub());

        if ($configure !== null) {
            $configure($container);
        }

        (new TypeLangMapperServiceProvider($container))->register();

        return $container;
    }
}
