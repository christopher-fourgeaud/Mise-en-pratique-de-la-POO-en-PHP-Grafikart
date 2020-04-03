<?php

namespace App\Blog\Actions;

use Framework\Router;
use App\Blog\Table\CategoryTable;
use Framework\Actions\CrudAction;
use Framework\Session\FlashService;
use Framework\Renderer\RendererInterface;
use Psr\Http\Message\ServerRequestInterface;

class CategoryCrudAction extends CrudAction
{

    /**
     * Chemin de la vue
     *
     * @var string
     */
    protected $viewPath = "@blog/admin/categories";

    /**
     * Préfix de la route
     *
     * @var string
     */
    protected $routePrefix = "blog.category.admin";

    protected $acceptedParams = ['name', 'slug'];

    public function __construct(RendererInterface $renderer, Router $router, CategoryTable $table, FlashService $flash)
    {
        parent::__construct($renderer, $table, $router, $flash);
    }

    protected function getValidator(ServerRequestInterface $request)
    {
        return parent::getValidator($request)
            ->required('name', 'slug')
            ->checkLength('name', 2, 250)
            ->checkLength('slug', 2, 50)
            ->checkUnique('slug', $this->table->getTable(), $this->table->getPdo(), $request->getAttribute('id'))
            ->checkSlug('slug');
    }
}
