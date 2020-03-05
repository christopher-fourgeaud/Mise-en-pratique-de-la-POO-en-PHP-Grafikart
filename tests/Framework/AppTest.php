<?php

namespace Tests\Framework;

use Exception;
use Framework\App;
use App\blog\BlogModule;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Tests\Framework\Modules\WrongModule;
use Tests\Framework\Modules\StringModule;

class AppTest extends TestCase
{
    public function testRedirectTrailingSlash()
    {
        $app = new App;
        $request = new ServerRequest('GET', '/demoslash/');
        $response = $app->run($request);

        $this->assertContains('/demoslash', $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testBlog()
    {
        $app = new App([
            BlogModule::class
        ]);
        $request = new ServerRequest('GET', '/blog');
        $response = $app->run($request);

        $this->assertStringContainsString('<h1>Bienvenue sur le blog</h1>', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());

        $requestPageArticle = new ServerRequest('GET', '/blog/article-de-test');
        $responsePageArticle = $app->run($requestPageArticle);
        $this->assertStringContainsString('<h1>Bienvenue sur l\'article article-de-test</h1>', $responsePageArticle->getBody());
    }

    public function testThrowExceptionIfNoResponseSent()
    {
        $app = new App([
            WrongModule::class
        ]);
        $request = new ServerRequest('Get', '/demo');
        $this->expectException(Exception::class);
        $app->run($request);
    }

    public function testConvertStringToResponse()
    {
        $app = new App([
            StringModule::class
        ]);
        $request = new ServerRequest('Get', '/demo');
        $response  = $app->run($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertStringContainsString('DEMO', (string) $response->getBody());
    }

    public function testError404()
    {
        $app = new App;
        $request = new ServerRequest('GET', '/aze');
        $response = $app->run($request);

        $this->assertStringContainsString('<h1>Erreur 404</h1>', $response->getBody());
        $this->assertEquals(404, $response->getStatusCode());
    }
}
