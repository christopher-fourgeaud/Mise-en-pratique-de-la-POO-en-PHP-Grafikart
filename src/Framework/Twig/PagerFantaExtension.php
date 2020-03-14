<?php

namespace Framework\Twig;

use Framework\Router;
use Pagerfanta\Pagerfanta;
use Twig\Extension\AbstractExtension;
use Pagerfanta\View\TwitterBootstrap4View;
use Twig\TwigFunction;

class PagerFantaExtension extends AbstractExtension
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
            new TwigFunction('paginate', [$this, 'paginate'], ['is_safe' => ['html']])
        ];
    }

    public function paginate(Pagerfanta $paginatedResults, string $route, array $queryArgs = []): string
    {
        $view = new TwitterBootstrap4View();
        return $view->render($paginatedResults, function (int $page) use ($route, $queryArgs) {
            if ($page > 1) {
                $queryArgs['p'] = $page;
            }
            return $this->router->generateUrl($route, [], $queryArgs);
        });
    }
}
