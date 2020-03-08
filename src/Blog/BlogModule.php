<?php

namespace App\blog;

use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BlogModule
 */
class BlogModule
{
    private $renderer;

    public function __construct(Router $router, RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        $this->renderer->addPath('blog', __DIR__ . '/views');

        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/{slug:[a-z\-0-9]+}', [$this, 'show'], 'blog.show');
    }

    /**
     * Fonction correspondant à la route blog.index
     *
     * @param Request $request
     * @return string
     */
    public function index(Request $request): string
    {
        return $this->renderer->render('@blog/index');
    }

    /**
     * Fonction correspondant à la route blog.show
     *
     * @param Request $request
     * @return string
     */
    public function show(Request $request): string
    {
        return $this->renderer->render('@blog/show', [
            'slug' => $request->getAttribute('slug')
        ]);
    }
}
