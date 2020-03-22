<?php

namespace App\Blog\Actions;

use App\Blog\Table\CategoryTable;
use Framework\Router;
use App\Blog\Table\PostTable;
use Psr\Http\Message\ResponseInterface;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostShowAction
{

    /**
     * Instance du Renderer
     *
     * @var RendererInterface
     */
    private $renderer;

    /**
     * Instance du router
     *
     * @var Router
     */
    private $router;

    /**
     * Instance de PostTable
     *
     * @var PostTable
     */
    private $postTable;


    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        PostTable $postTable,
        Router $router
    ) {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->router = $router;
    }

    /**
     * Fonction correspondant à la route blog.show
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function __invoke(Request $request)
    {
        $slug = $request->getAttribute('slug');

        // On récupère notre article
        $post = $this->postTable->findWithCategory($request->getAttribute('id'));

        if ($post->slug !== $slug) {
            return $this->redirect('blog.show', [
                'slug' => $post->slug,
                'id' => $post->id
            ]);
        }
        return $this->renderer->render('@blog/show', [
            'post' => $post
        ]);
    }
}
