<?php

namespace App\Account\Actions;

use App\Auth\UserTable;
use Framework\Auth;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class AccountEditAction
{

    /**
     * Instance de renderer
     *
     * @var RendererInterface
     */
    private $renderer;

    /**
     * Instance de Auth
     *
     * @param Auth $auth
     */
    private $auth;

    /**
     * Instance de FlashService
     *
     * @var FlashService
     */
    private $flash;

    /**
     * Instance de UserTable
     *
     * @var UserTable
     */
    private $userTable;

    public function __construct(
        RendererInterface $renderer,
        Auth $auth,
        FlashService $flash,
        UserTable $userTable
    ) {
        $this->renderer = $renderer;
        $this->auth = $auth;
        $this->flash = $flash;
        $this->userTable = $userTable;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $user = $this->auth->getUser();
        $params = $request->getParsedBody();
        $validator = (new Validator($params))
            ->confirm('password')
            ->required('firstname', 'lastname');
        if ($validator->isValid()) {
            $userParams = [
                'firstname' => $params['firstname'],
                'lastname'  => $params['lastname']
            ];
            if (!empty($params['password'])) {
                $userParams['password'] = password_hash($params['password'], PASSWORD_DEFAULT);
            }
            $this->userTable->update($user->id, $userParams);
            $this->flash->success('Votre compte a bien été mis à jour');
            return new RedirectResponse($request->getUri()->getPath());
        }
        $errors = $validator->getErrors();
        return $this->renderer->render('@account/account', [
            'errors' => $errors,
            'user' => $user
        ]);
    }
}
