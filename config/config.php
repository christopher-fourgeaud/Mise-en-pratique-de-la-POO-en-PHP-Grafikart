<?php

use Framework\Router;
use Framework\Session\PHPSession;
use Framework\Twig\CsrfExtension;
use Framework\Twig\FormExtension;
use Framework\Twig\TextExtension;
use Framework\Twig\TimeExtension;
use Framework\Twig\FlashExtension;
use Framework\Router\RouterFactory;
use Psr\Container\ContainerInterface;
use Framework\Session\SessionInterface;
use Framework\Twig\PagerFantaExtension;
use Framework\Middleware\CsrfMiddleware;
use Framework\Renderer\RendererInterface;
use Framework\Router\RouterTwigExtension;

use Framework\Renderer\TwigRendererFactory;
use Framework\SwiftMailerFactory;

use function DI\{autowire, factory, get, env};

return [
    'env' => env('ENV', 'production'),

    // Database
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => '',
    'database.name' => 'monsupersite',

    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
        get(RouterTwigExtension::class),
        get(PagerFantaExtension::class),
        get(TextExtension::class),
        get(TimeExtension::class),
        get(FlashExtension::class),
        get(FormExtension::class),
        get(CsrfExtension::class)

    ],
    SessionInterface::class => autowire(PHPSession::class),
    CsrfMiddleware::class => autowire()->constructor(get(SessionInterface::class)),
    Router::class => factory(RouterFactory::class),
    RendererInterface::class => factory(TwigRendererFactory::class),
    PDO::class => function (ContainerInterface $container) {
        return new PDO(
            'mysql:host=' . $container->get('database.host') . ';dbname=' . $container->get('database.name'),
            $container->get('database.username'),
            $container->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    },

    // Mailer
    'mail.to' => 'admin@admin.fr',
    Swift_Mailer::class => factory(SwiftMailerFactory::class)


];
