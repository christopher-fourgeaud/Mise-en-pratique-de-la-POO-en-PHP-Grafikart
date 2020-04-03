<?php

namespace Tests\App\Auth;

use Framework\Auth\User;
use PHPUnit\Framework\TestCase;
use App\Auth\ForbiddenMiddleware;
use Psr\Http\Message\UriInterface;
use Framework\Session\ArraySession;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Framework\Exception\ForbiddenException;
use Psr\Http\Message\ServerRequestInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;

class ForbiddenMiddlewareTest extends TestCase
{

    /**
     * @var SessionInterface
     */
    private $session;

    public function setUp(): void
    {
        $this->session = new ArraySession();
    }

    public function makeRequest($path = '/')
    {
        $uri = $this->getMockBuilder(UriInterface::class)->getMock();
        $uri->method('getPath')->willReturn($path);
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $request->method('getUri')->willReturn($uri);
        return $request;
    }

    public function makeDelegate()
    {
        $delegate = $this->getMockBuilder(DelegateInterface::class)->getMock();
        return $delegate;
    }

    public function makeMiddleware()
    {
        return new ForbiddenMiddleware('/login', $this->session);
    }

    public function testCatchForbiddenException()
    {
        $delegate = $this->makeDelegate();
        $delegate->expects($this->once())->method('process')->willThrowException(new ForbiddenException());
        $response = $this->makeMiddleware()->process($this->makeRequest('/test'), $delegate);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/login'], $response->getHeader('Location'));
        $this->assertEquals('/test', $this->session->get('auth.redirect'));
    }

    public function testCatchTypeErrorException()
    {
        $delegate = $this->makeDelegate();
        $delegate->expects($this->once())->method('process')->willReturnCallback(function (User $user) {
            return true;
        });
        $response = $this->makeMiddleware()->process($this->makeRequest('/test'), $delegate);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals(['/login'], $response->getHeader('Location'));
        $this->assertEquals('/test', $this->session->get('auth.redirect'));
    }

    public function testBubbleError()
    {
        $delegate = $this->makeDelegate();
        $delegate->expects($this->once())->method('process')->willReturnCallback(function () {
            throw new \TypeError("test", 200);
        });
        try {
            $this->makeMiddleware()->process($this->makeRequest('/test'), $delegate);
        } catch (\TypeError $e) {
            $this->assertEquals("test", $e->getMessage());
            $this->assertEquals(200, $e->getCode());
        }
    }

    public function testProcessValidRequest()
    {
        $delegate = $this->makeDelegate();
        $response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $delegate
            ->expects($this->once())
            ->method('process')
            ->willReturn($response);
        $this->assertSame($response, $this->makeMiddleware()->process($this->makeRequest('/test'), $delegate));
    }
}
