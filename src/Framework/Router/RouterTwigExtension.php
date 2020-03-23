<?php

namespace Framework\Router;

use Framework\Router;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class RouterTwigExtension extends AbstractExtension
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

    public function getFunctions()
    {
        return [
            new TwigFunction('path', [$this, 'pathFor']),
            new TwigFunction('is_subpath', [$this, 'isSubPath']),
        ];
    }

    public function pathFor(string $path, array $params = []): string
    {
        return $this->router->generateUrl($path, $params);
    }

    public function isSubPath(string $path): bool
    {
        $url = $_SERVER['REQUEST_URI'] ?? '/';
        $expectedUrl = $this->router->generateUrl($path);

        return strpos($url, $expectedUrl) !== false;
    }
}
