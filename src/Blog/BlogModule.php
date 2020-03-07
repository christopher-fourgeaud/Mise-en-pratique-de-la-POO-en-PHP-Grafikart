<?php

namespace App\blog;

use Framework\Router;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BlogModule
 */
class BlogModule
{
    public function __construct(Router $router)
    {
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/{slug:[a-z\-]+}', [$this, 'show'], 'blog.show');
    }

    /**
     * Fonction correspondant à la route blog.index
     *
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        return '<h1>Bienvenue sur le blog</h1>';
    }

    /**
     * Fonction correspondant à la route blog.show
     *
     * @param Request $request
     * @return string
     */
    public function show(Request $request): string
    {
        return '<h1>Bienvenue sur l\'article ' . $request->getAttribute('slug') . '</h1>';
    }
}
