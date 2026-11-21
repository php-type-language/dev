<?php

declare(strict_types=1);

use TypeLang\Mapper\Bridge\Laravel\Tests\Stub\UserStub;

return [
    UserStub::class => [
        'properties' => [
            'userName' => [
                'name' => 'login',
            ],
        ],
    ],
];
