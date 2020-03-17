<?php

namespace App\Blog\Actions;

use Framework\Router;
use App\Blog\Table\PostTable;
use Psr\Http\Message\ResponseInterface;
use Framework\Actions\RouterAwareAction;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminBlogAction
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
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string) $request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }

    /**
     * Fonction correspondant à la route blog.index
     *
     * @return string
     */
    public function index(Request $request): string
    {
        $params = $request->getQueryParams();
        $items = $this->postTable->findPaginated(12, $params['p'] ?? 1);

        return $this->renderer->render('@blog/admin/index', [
            'items' => $items
        ]);
    }

    /**
     * Edite un article
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function edit(Request $request)
    {
        $item = $this->postTable->find($request->getAttribute('id'));

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);

            $params['updated_at'] = date('Y-m-d H:i:s');

            $this->postTable->update($item->id, $params);
            return $this->redirect('blog.admin.index');
        }

        return $this->renderer->render('@blog/admin/edit', [
            'item' => $item
        ]);
    }

    /**
     * Crée un article
     *
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function create(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $params = array_merge($params, [
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')

            ]);
            $this->postTable->insert($params);
            return $this->redirect('blog.admin.index');
        }
        return $this->renderer->render('@blog/admin/create');
    }

    public function delete(Request $request)
    {
        $this->postTable->delete($request->getAttribute('id'));

        return $this->redirect('blog.admin.index');
    }

    private function getParams(Request $request)
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'content', 'slug']);
        }, ARRAY_FILTER_USE_KEY);
    }
}
