<?php

namespace Framework\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;

class CombinedMiddleware implements MiddlewareInterface
{
    /**
     * @var string[]
     */
    private $middlewares = [];
    /**
     * Instance de Container
     *
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, array $middlewares)
    {
        $this->middlewares = $middlewares;
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        $delegate = new CombinedMiddlewareDelegate($this->container, $this->middlewares, $delegate);

        return $delegate->process($request);
    }
}
