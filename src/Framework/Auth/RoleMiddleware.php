<?php

namespace Framework\Auth;

use Framework\Auth;
use Psr\Http\Message\ResponseInterface;
use Framework\Exception\ForbiddenException;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;

class RoleMiddleware implements MiddlewareInterface
{

    private $role;

    private $auth;

    public function __construct(Auth $auth, string $role)
    {
        $this->auth = $auth;
        $this->role = $role;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        $user = $this->auth->getUser();
        if ($user === null || !in_array($this->role, $user->getRoles())) {
            throw new ForbiddenException;
        }
        return $delegate->process($request);
    }
}
