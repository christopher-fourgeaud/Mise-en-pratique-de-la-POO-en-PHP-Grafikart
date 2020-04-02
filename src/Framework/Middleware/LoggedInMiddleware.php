<?php

namespace Framework\Middleware;

use Framework\Auth;
use Framework\Exception\ForbiddenException;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;

class LoggedInMiddleware implements MiddlewareInterface
{

    /*
    * @var Auth
     */
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        $user = $this->auth->getUser();
        if (is_null($user)) {
            throw new ForbiddenException();
        }
        return $delegate->process($request->withAttribute('user', $user));
    }
}
