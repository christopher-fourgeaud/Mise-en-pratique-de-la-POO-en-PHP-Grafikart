<?php

namespace App\Auth\Actions;

use App\Auth\Mailer\PasswordResetMailer;
use App\Auth\UserTable;
use Framework\Validator;
use Framework\Database\NoRecordException;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class PasswordForgetAction
{

    /**
     * Instance de RendererInterface
     *
     * @var RendererInterface
     */
    private $renderer;

    /**
     * Instance de UserTable
     *
     * @var UserTable
     */
    private $userTable;

    /**
     * Instance de PasswordResetMailer
     *
     * @var PasswordResetMailer
     */
    private $mailer;

    /**
     * Instance de FlashService
     *
     * @var FlashService
     */
    private $flashService;

    public function __construct(
        RendererInterface $renderer,
        UserTable $userTable,
        PasswordResetMailer $mailer,
        FlashService $flashService
    ) {
        $this->renderer = $renderer;
        $this->userTable = $userTable;
        $this->mailer = $mailer;
        $this->flashService = $flashService;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'GET') {
            return $this->renderer->render('@auth/password');
        }
        $params = $request->getParsedBody();
        $validator = (new Validator($params))
            ->notEmpty('email')
            ->checkEmail('email');
        if ($validator->isValid()) {
            try {
                $user = $this->userTable->findBy('email', $params['email']);
                $token = $this->userTable->resetPassword($user->id);
                $this->mailer->send($user->email, [
                    'id' => $user->id,
                    'token' => $token
                ]);
                $this->flashService->success('Un email vous a été envoyé');
                return new RedirectResponse($request->getUri()->getPath());
            } catch (NoRecordException $e) {
                $errors = ['email' => 'Aucun utilisateur ne correspon à cet email'];
            }
        } else {
            $errors = $validator->getErrors();
        }
        return $this->renderer->render('@auth/password', [
            'errors' => $errors
        ]);
    }
}
