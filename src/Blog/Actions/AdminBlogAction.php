<?php

namespace App\Blog\Actions;

use Framework\Router;
use Framework\Validator;
use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use DateTime;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
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

    /**
     * Instance de FlashService
     *
     * @var FlashService
     */
    private $flash;

    use RouterAwareAction;

    public function __construct(
        RendererInterface $renderer,
        PostTable $postTable,
        Router $router,
        FlashService $flash
    ) {
        $this->renderer = $renderer;
        $this->postTable = $postTable;
        $this->router = $router;
        $this->flash = $flash;
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
        $errors = [];

        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator =  $this->getValidator($request);

            if ($validator->isValid()) {
                $this->postTable->update($item->id, $params);
                $this->flash->success('L\'article a bien été modifié');
                return $this->redirect('blog.admin.index');
            }
            $errors = $validator->getErrors();
            $params['id'] = $item->id;
            $item = $params;
        }

        return $this->renderer->render('@blog/admin/edit', [
            'item' => $item,
            'errors' => $errors
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
        $errors = [];
        if ($request->getMethod() === 'POST') {
            $params = $this->getParams($request);
            $validator =  $this->getValidator($request);
            if ($validator->isValid()) {
                $this->postTable->insert($params);
                $this->flash->success('L\'article a bien été crée');
                return $this->redirect('blog.admin.index');
            }

            $errors = $validator->getErrors();
            $item = $params;
        }
        $item = new Post();
        $item->created_at = new DateTime();

        return $this->renderer->render('@blog/admin/create', [
            'errors' => $errors,
            'item' => $item
        ]);
    }

    public function delete(Request $request)
    {
        $this->postTable->delete($request->getAttribute('id'));

        return $this->redirect('blog.admin.index');
    }

    private function getParams(Request $request)
    {

        $params =  array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'content', 'slug', 'created_at']);
        }, ARRAY_FILTER_USE_KEY);

        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function getValidator(Request $request)
    {
        return (new Validator($request->getParsedBody()))
            ->required('content', 'name', 'slug', 'created_at')
            ->checkLength('content', 10)
            ->checkLength('name', 2, 250)
            ->checkLength('slug', 2, 50)
            ->checkDateTime('created_at')
            ->checkSlug('slug');
    }
}
