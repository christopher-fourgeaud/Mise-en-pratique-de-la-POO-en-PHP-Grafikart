<?php

namespace App\Account\Actions;

use App\Auth\User;
use Framework\Router;
use App\Auth\UserTable;
use Framework\Validator;
use App\Auth\DatabaseAuth;
use Framework\Database\Hydrator;
use Framework\Response\RedirectResponse;
use Framework\Renderer\RendererInterface;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class SignupAction
{
    private $renderer;

    private $userTable;

    private $router;

    private $auth;

    private $flashService;

    public function __construct(
        RendererInterface $renderer,
        UserTable $userTable,
        Router $router,
        DatabaseAuth $auth,
        FlashService $flashService
    ) {
        $this->router = $router;
        $this->renderer = $renderer;
        $this->userTable = $userTable;
        $this->auth = $auth;
        $this->flashService = $flashService;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'GET') {
            return $this->renderer->render('@account/signup');
        }
        $params = $request->getParsedBody();
        $validator = (new Validator($params))
            ->required('username', 'email', 'password', 'password_confirm')
            ->checkLength('username', 5)
            ->checkEmail('email')
            ->confirm('password')
            ->checkLength('password', 4)
            ->checkUnique('username', $this->userTable)
            ->checkUnique('email', $this->userTable);
        if ($validator->isValid()) {
            $userParams = [
                'username' => $params['username'],
                'email' => $params['email'],
                'password' => password_hash($params['password'], PASSWORD_DEFAULT)
            ];
            $this->userTable->insert($userParams);
            $user = Hydrator::hydrate($userParams, User::class);
            $user->id = $this->userTable->getPdo()->lastInsertId();
            $this->auth->setUser($user);
            $this->flashService->success('Votre compte à bien été crée');
            return new RedirectResponse($this->router->generateUrl('account'));
        }
        $errors = $validator->getErrors();
        return $this->renderer->render('@account/signup', [
            'errors' => $errors,
            'user'  => [
                'username' => $params['username'],
                'email' => $params['email']
            ]
        ]);
    }
}
