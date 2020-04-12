<?php

use Framework\App;
use Framework\Auth;
use Middlewares\Whoops;
use App\Auth\AuthModule;
use App\blog\BlogModule;
use App\Admin\AdminModule;
use App\Account\AccountModule;

use App\Contact\ContactModule;
use function Http\Response\send;
use App\Auth\ForbiddenMiddleware;
use Framework\Auth\RoleMiddleware;
use Framework\Auth\RoleMiddlewareFactory;
use GuzzleHttp\Psr7\ServerRequest;
use Framework\Middleware\CsrfMiddleware;
use Framework\Middleware\MethodMiddleware;
use Framework\Middleware\RouterMiddleware;
use Framework\Middleware\LoggedInMiddleware;
use Framework\Middleware\NotFoundMiddleware;
use Framework\Middleware\DispatcherMiddleware;
use Framework\Middleware\RendererRequestMiddleware;
use Framework\Middleware\TrailingSlashMiddleware;

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

$app = (new App('config/config.php'))
    ->addModule(AdminModule::class)
    ->addModule(ContactModule::class)
    ->addModule(BlogModule::class)
    ->addModule(AuthModule::class)
    ->addModule(AccountModule::class);
$container = $app->getContainer();
$app->pipe(Whoops::class)
    ->pipe(TrailingSlashMiddleware::class)
    ->pipe(ForbiddenMiddleware::class)
    ->pipe(
        $container->get('admin.prefix'),
        $container->get(RoleMiddlewareFactory::class)->makeForRole('admin')
    )
    ->pipe(MethodMiddleware::class)
    ->pipe(RendererRequestMiddleware::class)
    ->pipe(CsrfMiddleware::class)
    ->pipe(RouterMiddleware::class)
    ->pipe(DispatcherMiddleware::class)
    ->pipe(NotFoundMiddleware::class);

if (php_sapi_name() !== "cli") {
    $response = $app->run(ServerRequest::fromGlobals());
    send($response);
}
