<?php

namespace App\Auth\Actions;

use App\Auth\User;
use Framework\Router;
use App\Auth\UserTable;
use DateTime;
use Framework\Session\FlashService;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Validator;
use Psr\Http\Message\ServerRequestInterface;

class PasswordResetAction
{
    private $renderer;
    private $userTable;
    private $router;
    private $flashService;

    public function __construct(
        RendererInterface $renderer,
        UserTable $userTable,
        FlashService $flashService,
        Router $router
    ) {
        $this->renderer = $renderer;
        $this->userTable = $userTable;
        $this->flashService = $flashService;
        $this->router = $router;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        /**
         * @var User $user
         */
        $user = $this->userTable->find($request->getAttribute('id'));
        if (
            $user->getPasswordReset() !== null &&
            $user->getPasswordReset() === $request->getAttribute('token') &&
            time() - $user->getPasswordResetAt()->getTimestamp() < 600
        ) {
            if ($request->getMethod() === 'GET') {
                return $this->renderer->render('@auth/reset');
            } else {
                $params = $request->getParsedBody();
                $validator = (new Validator($params))
                    ->checkLength('password', 4)
                    ->confirm('password');
                if ($validator->isValid()) {
                    $this->userTable->updatePassword($user->getId(), $params['password']);
                    $this->flashService->success('Votre mot de passe a bien été changé');
                    return new RedirectResponse($this->router->generateUrl('auth.login'));
                } else {
                    $errors = $validator->getErrors();
                    return $this->renderer->render('@auth/reset', [
                        'errors' => $errors
                    ]);
                }
            }
        } else {
            $this->flashService->error('Token invalide');
            return new RedirectResponse($this->router->generateUrl('auth.password'));
        }
    }
}
