<?php

namespace App\blog;

use Framework\Module;
use Framework\Router;
use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\PostShowAction;
use App\Blog\Actions\PostIndexAction;
use Psr\Container\ContainerInterface;
use App\Blog\Actions\CategoryCrudAction;
use App\Blog\Actions\CategoryShowAction;
use Framework\Renderer\RendererInterface;

/**
 * Class BlogModule
 */
class BlogModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';

    const MIGRATIONS = __DIR__ . '/db/migrations';

    const SEEDS = __DIR__ . '/db/seeds';


    public function __construct(ContainerInterface $container)
    {
        $blogPrefix = $container->get('blog.prefix');
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/views');
        $router = $container->get(Router::class);
        $router->get($blogPrefix, PostIndexAction::class, 'blog.index');
        $router->get(
            "$blogPrefix/{slug:[a-z\-0-9]+}-{id:[0-9]+}",
            PostShowAction::class,
            'blog.show'
        );
        $router->get(
            "$blogPrefix/category/{slug:[a-z\-0-9]+}",
            CategoryShowAction::class,
            'blog.category'
        );

        if ($container->has('admin.prefix')) {
            $prefix = $container->get('admin.prefix');

            $router->crud("$prefix/posts", PostCrudAction::class, 'blog.admin');
            $router->crud("$prefix/categories", CategoryCrudAction::class, 'blog.category.admin');
        }
    }
}
