<?php

namespace App\Blog\Actions;

use App\Blog\Entity\Post;
use App\Blog\PostUpload;
use App\Blog\Table\CategoryTable;
use App\Blog\Table\PostTable;
use DateTime;
use Framework\Actions\CrudAction;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class PostCrudAction extends CrudAction
{

    /**
     * Chemin de la vue
     *
     * @var string
     */
    protected $viewPath = "@blog/admin/posts";

    /**
     * Préfix de la route
     *
     * @var string
     */
    protected $routePrefix = "blog.admin";

    /**
     * Instance de CategoryTable
     *
     * @var CategoryTable
     */
    protected $categoryTable;

    /**
     * Instance de PostUpload
     *
     * @var PostUpload
     */
    private $postUpload;

    public function __construct(
        RendererInterface $renderer,
        Router $router,
        PostTable $table,
        FlashService $flash,
        CategoryTable $categoryTable,
        PostUpload $postUpload
    ) {
        parent::__construct($renderer, $table, $router, $flash);
        $this->categoryTable = $categoryTable;
        $this->postUpload = $postUpload;
    }

    public function delete(ServerRequestInterface $request)
    {
        $post = $this->table->find($request->getAttribute('id'));
        $this->postUpload->delete($post->image);
        return parent::delete($request);
    }

    protected function formParams(array $params): array
    {
        $params['categories'] = $this->categoryTable->findList();
        $params['categories']['12123313'] = 'Categorie Fake';
        return $params;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Post $post
     * @return array
     */
    protected function getParams(ServerRequestInterface $request, $post): array
    {
        $params = array_merge($request->getParsedBody(), $request->getUploadedFiles());
        // Uploader le fichier
        $params['image'] = $this->postUpload->upload($params['image'], $post->image);
        $params =  array_filter($params, function ($key) {
            return in_array($key, ['name', 'content', 'slug', 'created_at', 'category_id', 'image']);
        }, ARRAY_FILTER_USE_KEY);

        return array_merge($params, [
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    protected function getValidator(ServerRequestInterface $request)
    {
        $validator = parent::getValidator($request)
            ->required('content', 'name', 'slug', 'created_at', 'category_id')
            ->checkLength('content', 10)
            ->checkLength('name', 2, 250)
            ->checkLength('slug', 2, 50)
            ->checkExists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
            ->checkDateTime('created_at')
            ->checkExtension('image', ['jpg', 'png'])
            ->checkSlug('slug');
        if (is_null($request->getAttribute('id'))) {
            $validator->checkUploaded('image');
        }
        return $validator;
    }

    protected function getNewEntity()
    {
        $post = new Post();
        $post->created_at = new DateTime();

        return $post;
    }
}
