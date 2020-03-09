<?php

namespace App\Blog\Actions;

use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogAction
{

    /**
     * Instance du Renderer
     *
     * @var RendererInterface
     */
    private $renderer;

    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request)
    {
        $slug = $request->getAttribute('slug');
        if ($slug) {
            return $this->show($slug);
        }
        return $this->index();
    }

    /**
     * Fonction correspondant à la route blog.index
     *
     * @return string
     */
    public function index(): string
    {
        return $this->renderer->render('@blog/index');
    }

    /**
     * Fonction correspondant à la route blog.show
     * @return string
     */
    public function show(string $slug): string
    {
        return $this->renderer->render('@blog/show', [
            'slug' => $slug
        ]);
    }
}
