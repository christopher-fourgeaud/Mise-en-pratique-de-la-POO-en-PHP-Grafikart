<?php

namespace Framework;

use Framework\Router\Route;
use Zend\Expressive\Router\FastRouteRouter;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\Route as ZendRoute;

/**
 * Class Router
 * Enregistre et compare les routes
 */
class Router
{

    /**
     * @var FastRouteRouter
     */
    private $router;

    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }

    /**
     * Récupère la requète en get
     *
     * @param string $url
     * @param string|callable $callable
     * @param string $route_name
     * @return void
     */
    public function get(string $url, $callable, ?string $route_name = null)
    {
        $this->router->addRoute(new ZendRoute($url, $callable, ['GET'], $route_name));
    }


    /**
     * Récupère la requète en post
     *
     * @param string $url
     * @param string|callable $callable
     * @param string $route_name
     * @return void
     */
    public function post(string $url, $callable, ?string $route_name = null)
    {
        $this->router->addRoute(new ZendRoute($url, $callable, ['POST'], $route_name));
    }

    /**
     * Récupère la requète en post
     *
     * @param string $url
     * @param string|callable $callable
     * @param string $route_name
     * @return void
     */
    public function delete(string $url, $callable, ?string $route_name = null)
    {
        $this->router->addRoute(new ZendRoute($url, $callable, ['DELETE'], $route_name));
    }

    /**
     * Compare la requète avec nos routes
     *
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request);
        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedMiddleware(),
                $result->getMatchedParams()
            );
        } else {
            return null;
        }
    }

    /**
     * Génère les routes du crud
     *
     * @param string $prefixPath
     * @param string $callable
     * @param string $prefixname
     * @return void
     */
    public function crud(string $prefixPath, $callable, string $prefixname)
    {
        // Index
        $this->get("$prefixPath", $callable, "$prefixname.index");

        // Create
        $this->get("$prefixPath/new", $callable, "$prefixname.create");
        $this->post("$prefixPath/new", $callable);

        // Edit
        $this->get("$prefixPath/{id:\d+}", $callable, "$prefixname.edit");
        $this->post("$prefixPath/{id:\d+}", $callable);

        // Delete
        $this->delete("$prefixPath/{id:\d+}", $callable, "$prefixname.delete");
    }

    public function generateUrl(string $name, array $params = [], array $queryParams = []): ?string
    {
        $url = $this->router->generateUri($name, $params);
        if (!empty($queryParams)) {
            return $url . '?' . http_build_query($queryParams);
        }
        return $url;
    }
}
