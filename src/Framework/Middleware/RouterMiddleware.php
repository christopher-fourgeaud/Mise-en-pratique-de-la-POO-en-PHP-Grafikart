<?php

namespace Framework\Middleware;

use Framework\Router;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;

class RouterMiddleware
{

    /**
     * Instance du router
     *
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $route = $this->router->match($request);

        // Si $route est null on renvoie une 404
        if (is_null($route)) {
            return $next($request);
        }

        // Récupère les paramètres de la route
        $params = $route->getParams();

        // Je modifie la requète que j'envoie à mon callback pour lui rajouter les attributs dont il à besoin
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);

        $request = $request->withAttribute(get_class($route), $route);

        return $next($request);
    }
}
