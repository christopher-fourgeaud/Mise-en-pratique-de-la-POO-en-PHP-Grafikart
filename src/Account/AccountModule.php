<?php

namespace App\Account;

use Framework\Module;
use Framework\Router;
use App\Account\Actions\SignupAction;
use App\Account\Actions\AccountAction;
use Framework\Renderer\RendererInterface;
use App\Account\Actions\AccountEditAction;
use Framework\Middleware\LoggedInMiddleware;

class AccountModule extends Module
{
    const MIGRATIONS = __DIR__ . '/migrations';

    const DEFINITIONS = __DIR__ . '/definitions.php';

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('account', __DIR__ . '/views');
        $router->get('/inscription', SignupAction::class, 'account.signup');
        $router->post('/inscription', SignupAction::class);
        $router->get('/mon-profil', [LoggedInMiddleware::class, AccountAction::class], 'account');
        $router->post('/mon-profil', [LoggedInMiddleware::class, AccountEditAction::class]);
    }
}
