<?php

namespace Tests\App\Blog\Actions;

use Framework\Router;
use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use App\Blog\Actions\PostShowAction;
use Framework\Renderer\RendererInterface;

class PostShowActionTest extends TestCase
{
    /**
     * @var PostShowAction
     */
    private $action;

    private $renderer;

    private $postTable;

    private $router;

    public function setUp(): void
    {
        $this->renderer = $this->prophesize(RendererInterface::class);
        $this->postTable = $this->prophesize(PostTable::class);
        $this->router = $this->prophesize(Router::class);
        $this->action = new PostShowAction(
            $this->renderer->reveal(),
            $this->postTable->reveal(),
            $this->router->reveal()
        );
    }

    public function makePost(int $id, string $slug): Post
    {
        // Article
        $post = new Post();
        $post->id = $id;
        $post->slug = $slug;
        return $post;
    }

    public function testShowRedirect()
    {
        $post = $this->makePost(9, "azezae-azeazae");
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', 'demo');

        $this->router->generateUrl(
            'blog.show',
            ['id' => $post->id, 'slug' => $post->slug]
        )->willReturn('/demo2');
        $this->postTable->findWithCategory($post->id)->willReturn($post);

        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/demo2'], $response->getHeader('location'));
    }

    public function testShowRender()
    {
        $post = $this->makePost(9, "azezae-azeazae");
        $request = (new ServerRequest('GET', '/'))
            ->withAttribute('id', $post->id)
            ->withAttribute('slug', $post->slug);
        $this->postTable->findWithCategory($post->id)->willReturn($post);
        $this->renderer->render('@blog/show', ['post' => $post])->willReturn('');

        $response = call_user_func_array($this->action, [$request]);
        $this->assertEquals(true, true);
    }
}
