<?php

use Framework\Router;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router\RouterTwigExtension;

use function DI\{autowire, factory, get};

return [
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
        get(RouterTwigExtension::class)
    ],
    Router::class => autowire(),
    RendererInterface::class => factory(TwigRendererFactory::class),

];
