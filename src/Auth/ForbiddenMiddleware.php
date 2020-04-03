<?php

namespace App\Auth;

use TypeError;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Framework\Response\RedirectResponse;
use Framework\Exception\ForbiddenException;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;

class ForbiddenMiddleware implements MiddlewareInterface
{
    private $loginPath;


    private $session;

    public function __construct(string $loginPath, SessionInterface $session)
    {
        $this->loginPath = $loginPath;
        $this->session = $session;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        try {
            return $delegate->process($request);
        } catch (ForbiddenException $exception) {
            return $this->redirectLogin($request);
        } catch (\TypeError $error) {
            if (strpos($error->getMessage(), \Framework\Auth\User::class) !== false) {
                return $this->redirectLogin($request);
            }
            throw $error;
        }
    }

    public function redirectLogin(ServerRequestInterface $request): ResponseInterface
    {
        $this->session->set('auth.redirect', $request->getUri()->getPath());
        (new FlashService($this->session))->error('Vous devez posséder un compte pour accéder à cette page');
        return new RedirectResponse($this->loginPath);
    }
}
