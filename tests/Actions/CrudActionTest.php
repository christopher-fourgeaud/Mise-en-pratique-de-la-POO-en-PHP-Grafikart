<?php

namespace Tests\Framework\Actions;

use ReflectionClass;
use Framework\Router;
use App\Blog\Entity\Post;
use Pagerfanta\Pagerfanta;
use Framework\Database\Query;
use Framework\Database\Table;
use PHPUnit\Framework\TestCase;
use Framework\Actions\CrudAction;
use GuzzleHttp\Psr7\ServerRequest;
use Framework\Session\FlashService;
use Pagerfanta\Adapter\ArrayAdapter;
use Framework\Renderer\RendererInterface;

class CrudActionTest extends TestCase
{

    private $flash;
    private $table;

    public function setUp(): void
    {
        $this->table = $this->getMockBuilder(Table::class)->disableOriginalConstructor()->getMock();
        $this->query = $this->getMockBuilder(Query::class)->getMock();
        $this->table->method('findAll')->willReturn($this->query);
        $this->table->method('find')->willReturnCallback(function ($id) {
            $object = new \stdClass();
            $object->id = (int) $id;
            return $object;
        });
        $this->flash = $this->getMockBuilder(FlashService::class)->disableOriginalConstructor()->getMock();
        $this->renderer = $this->getMockBuilder(RendererInterface::class)->getMock();
    }

    private function makeCrudAction()
    {
        $this->renderer->method('render')->willReturn('');
        $router = $this->getMockBuilder(Router::class)->getMock();
        $router->method('generateUrl')->willReturnCallback(function ($url) {
            return $url;
        });
        $action = new CrudAction($this->renderer, $this->table, $router, $this->flash);
        $property = (new \ReflectionClass($action))->getProperty('viewPath');
        $property->setAccessible(true);
        $property->setValue($action, '@demo');
        $property = (new \ReflectionClass($action))->getProperty('acceptedParams');
        $property->setAccessible(true);
        $property->setValue($action, ['name']);
        $property = (new ReflectionClass($action))->getProperty('acceptedParams');
        $property->setAccessible(true);
        $property->setValue($action, ['name']);
        return $action;
    }

    public function testIndex()
    {
        $request = new ServerRequest('GET', '/demo');
        $pager = new Pagerfanta(new ArrayAdapter([1, 2]));
        $this->query->method('paginate')->willReturn($pager);
        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with('@demo/index', ['items' => $pager]);
        call_user_func($this->makeCrudAction(), $request);
    }

    public function testEdit()
    {
        $id = 3;
        $request = (new ServerRequest('GET', '/demo'))->withAttribute('id', $id);
        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                '@demo/edit',
                $this->callback(function ($params) use ($id) {
                    $this->assertClassHasAttribute('id', Post::class);
                    return true;
                })
            );
        call_user_func($this->makeCrudAction(), $request);
    }

    public function testEditWithParams()
    {
        $id = 3;
        $request = (new ServerRequest('POST', '/demo'))
            ->withAttribute('id', $id)
            ->withParsedBody(['name' => 'demo']);
        $this->table
            ->expects($this->once())
            ->method('update')
            ->with($id, ['name' => 'demo']);
        $response = call_user_func($this->makeCrudAction(), $request);
        $this->assertEquals(['.index'], $response->getHeader('Location'));
    }

    public function testDelete()
    {
        $id = 3;
        $request = (new ServerRequest('DELETE', '/demo'))
            ->withAttribute('id', $id);
        $this->table
            ->expects($this->once())
            ->method('delete')
            ->with($id);
        $response = call_user_func($this->makeCrudAction(), $request);
        $this->assertEquals(['.index'], $response->getHeader('Location'));
    }

    public function testCreate()
    {
        $id = 3;
        $request = (new ServerRequest('GET', '/new'))->withAttribute('id', $id);
        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                '@demo/create',
                $this->callback(function ($params) use ($id) {
                    $this->assertInstanceOf(\stdClass::class, $params['item']);
                    return true;
                })
            );
        call_user_func($this->makeCrudAction(), $request);
    }

    public function testCreateWithParams()
    {
        $request = (new ServerRequest('POST', '/new'))
            ->withParsedBody(['name' => 'demo']);
        $this->table
            ->expects($this->once())
            ->method('insert')
            ->with(['name' => 'demo']);
        $response = call_user_func($this->makeCrudAction(), $request);
        $this->assertEquals(['.index'], $response->getHeader('Location'));
    }
}
