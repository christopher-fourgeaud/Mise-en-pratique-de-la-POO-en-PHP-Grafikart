<?php

use App\Auth\AuthTwigExtension;
use App\Auth\DatabaseAuth;
use App\Auth\ForbiddenMiddleware;
use Framework\Auth;

use function DI\add;
use function DI\autowire;
use function DI\get;

return [
    'auth.login' => '/login',
    'twig.extensions' => add([
        get(AuthTwigExtension::class),
    ]),
    Auth::class => get(DatabaseAuth::class),
    ForbiddenMiddleware::class => autowire()->constructorParameter('loginPath', get('auth.login'))
];
