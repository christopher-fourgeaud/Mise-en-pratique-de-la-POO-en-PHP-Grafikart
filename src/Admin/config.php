<?php

use Framework\Router;
use App\Admin\AdminModule;
use App\Admin\AdminTwigExtension;
use App\Admin\DashboardAction;

use function DI\add;
use function DI\autowire;
use function DI\create;
use function DI\get;
use Framework\Renderer\RendererInterface;

return [
    'admin.prefix' => '/admin',
    'admin.widgets' => [],
    AdminTwigExtension::class => autowire()->constructor(get('admin.widgets')),
    AdminModule::class => autowire()->constructorParameter('prefix', get('admin.prefix')),
    DashboardAction::class => autowire()->constructorParameter('widgets', get('admin.widgets')),

];
