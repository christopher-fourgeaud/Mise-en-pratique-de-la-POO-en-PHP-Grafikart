<?php

use App\Auth\User;
use Framework\Auth;
use function DI\add;
use function DI\get;
use App\Auth\UserTable;

use function DI\autowire;
use App\Auth\DatabaseAuth;
use App\Auth\AuthTwigExtension;
use App\Auth\ForbiddenMiddleware;
use App\Auth\Mailer\PasswordResetMailer;

return [
    'auth.login' => '/login',
    'auth.entity' => User::class,
    'twig.extensions' => add([
        get(AuthTwigExtension::class),
    ]),
    Auth::class => get(DatabaseAuth::class),
    UserTable::class => autowire()->constructorParameter('entity', get('auth.entity')),
    ForbiddenMiddleware::class => autowire()->constructorParameter('loginPath', get('auth.login')),
    PasswordResetMailer::class => autowire()->constructorParameter('from', get('mail.from'))

];
