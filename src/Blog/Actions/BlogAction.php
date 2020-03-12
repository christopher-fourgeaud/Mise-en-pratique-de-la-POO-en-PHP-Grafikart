<?php

namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use PDO;
use Framework\Router;
use Framework\Renderer\RendererInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogAction
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

    public function __construct(RendererInterface $renderer, PostTable $postTable, Router $router)
    {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->router = $router;
    }

    public function __invoke(Request $request)
    {
        if ($request->getAttribute('id')) {
            return $this->show($request);
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
        $posts = $this->postTable->findPaginated();

        return $this->renderer->render('@blog/index', [
            'posts' => $posts
        ]);
    }

    /**
     * Fonction correspondant à la route blog.show
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function show(Request $request)
    {
        $slug = $request->getAttribute('slug');

        // On récupère notre article
        $post = $this->postTable->find($request->getAttribute('id'));

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
