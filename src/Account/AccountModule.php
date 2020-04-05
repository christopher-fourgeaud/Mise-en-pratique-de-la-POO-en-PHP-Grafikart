<?php

namespace App\Account;

use Framework\Module;
use Framework\Router;
use App\Account\Actions\SignupAction;
use Framework\Renderer\RendererInterface;

class AccountModule extends Module
{
    public function __construct(Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('account', __DIR__ . '/views');
        $router->get('/inscription', SignupAction::class, 'account.signup');
        $router->post('/inscription', SignupAction::class);
        $router->get('/mon-profil', SignupAction::class, 'account.profile');
    }
}
