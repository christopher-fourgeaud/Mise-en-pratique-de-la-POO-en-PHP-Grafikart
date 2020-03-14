<?php

namespace Tests\Framework;

use Framework\Router;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;

class RouterTest extends TestCase
{
    /**
     * @var Router
     */
    private $router;

    public function setUp(): void
    {
        $this->router = new Router();
    }

    public function testGetMethod()
    {
        // On crée une fausse requète en methode GET qui contient /blog
        $request = new ServerRequest('GET', '/blog');

        // On crée une nouvelle url, sa fonction callable et le nom de sa route
        $this->router->get('/blog', function () {
            return 'hello';
        }, 'blog');

        // On verifie si notre requète correspond à une des url qui à été entrée
        $route = $this->router->match($request);

        // Teste que l'on récupère bien le nom de la route
        $this->assertEquals('blog', $route->getName());

        // Teste que l'on récupère le bon résultat de la fonction callable 
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
    }

    public function testGetMethodIfUrlDoesNotExist()
    {
        // On crée une fausse requète en methode GET qui contient /blog
        $request = new ServerRequest('GET', '/blog');

        // On crée une nouvelle url, sa fonction callable et le nom de sa route
        $this->router->get('/blogy', function () {
            return 'hello';
        }, 'blog');

        // On verifie si notre requète correspond à une des url qui à été entrée
        $route = $this->router->match($request);

        // Teste que notre notre route n'existe pas
        $this->assertEquals(null, $route);
    }

    public function testGetMethodWithParameters()
    {
        // On crée une fausse requète en methode GET qui contient /blog
        $request = new ServerRequest('GET', '/blog/mon-slug-1');

        // On crée une nouvelle url, sa fonction callable et le nom de sa route
        $this->router->get('/blog', function () {
            return 'dsqsqd';
        }, 'posts');

        // On crée une nouvelle url, sa fonction callable et le nom de sa route
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () {
            return 'hello';
        }, 'post.show');

        // On verifie si notre requète correspond à une des url qui à été entrée
        $route = $this->router->match($request);

        // Teste que l'on récupère bien le nom de la route
        $this->assertEquals('post.show', $route->getName());

        // Teste que l'on récupère le bon résultat de la fonction callable 
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));

        // Teste que l'on récupère les paramètres de l'url
        $this->assertEquals(['slug' => 'mon-slug', 'id' => '1'], $route->getParams());

        // Teste quand une url est invalide
        $route = $this->router->match(new ServerRequest('GET', '/blog/mon_slug-8'));
        $this->assertEquals(null, $route);
    }

    public function testGenerateUrl()
    {
        // On crée une nouvelle url, sa fonction callable et le nom de sa route
        $this->router->get('/blog', function () {
            return 'dsqsqd';
        }, 'posts');

        // On crée une nouvelle url, sa fonction callable et le nom de sa route
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () {
            return 'hello';
        }, 'post.show');

        // On génère une url à partir d'un nom de route et de paramètres passé en tableau
        $url = $this->router->generateUrl('post.show', ['slug' => 'mon-article', 'id' => 18]);

        // On teste que notre url généré est correcte
        $this->assertEquals('/blog/mon-article-18', $url);
    }

    public function testGenerateUrlWithQueryParams()
    {
        // On crée une nouvelle url, sa fonction callable et le nom de sa route
        $this->router->get('/blog', function () {
            return 'dsqsqd';
        }, 'posts');

        // On crée une nouvelle url, sa fonction callable et le nom de sa route
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function () {
            return 'hello';
        }, 'post.show');

        // On génère une url à partir d'un nom de route, de paramètres d'url ainsi le paramètre de la page
        $url = $this->router->generateUrl(
            'post.show',
            ['slug' => 'mon-article', 'id' => 18],
            ['p' => 2]
        );

        // On teste que notre url généré est correcte
        $this->assertEquals('/blog/mon-article-18?p=2', $url);
    }
}
