<?php

namespace Tests\App\Contact;

use App\Contact\ContactAction;
use Framework\Renderer\RendererInterface;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Tests\ActionTestCase;
use Swift_Mailer;

class ContactActionTest extends ActionTestCase
{
    /**
     * @var ContactAction
     */
    private $action;

    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var FlashService
     */
    private $flashService;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $to = "demo@local.dev";

    public function setUp(): void
    {
        $this->renderer = $this->getMockBuilder(RendererInterface::class)->getMock();
        $this->flashService = $this->getMockBuilder(FlashService::class)->disableOriginalConstructor()->getMock();
        $this->mailer = $this->getMockBuilder(Swift_Mailer::class)->disableOriginalConstructor()->getMock();

        $this->action = new ContactAction($this->to, $this->renderer, $this->flashService, $this->mailer);
    }

    public function testGet()
    {
        $this->renderer->expects($this->once())
            ->method('render')
            ->with('@contact/contact')
            ->willReturn('');
        call_user_func($this->action, $this->makeRequest("/contact"));
    }

    public function testPostInvalid()
    {
        $request = $this->makeRequest('/contact', [
            'name' => 'Jean marc',
            'email' => 'azezae',
            'content' => 'azezaezaezeae'
        ]);
        $this->renderer
            ->expects($this->once())
            ->method('render')
            ->with(
                '@contact/contact',
                $this->callback(function ($params) {
                    $this->assertArrayHasKey('errors', $params);
                    $this->assertArrayHasKey('email', $params['errors']);
                    return true;
                })
            )
            ->willReturn('');
        $this->flashService->expects($this->once())->method('error');
        call_user_func($this->action, $request);
    }

    public function testPostValid()
    {
        $request = $this->makeRequest('/contact', [
            'name' => 'Jean marc',
            'email' => 'demo@local.dev',
            'content' => 'lorem lorem lorem lorem lorem '
        ]);
        $this->flashService->expects($this->once())->method('success');
        $this->mailer
            ->expects($this->once())
            ->method('send')
            ->with($this->callback(function (\Swift_Message $message) {
                $this->assertArrayHasKey($this->to, $message->getTo());
                $this->assertArrayHasKey('demo@local.dev', $message->getFrom());
                return true;
            }));
        $response = call_user_func($this->action, $request);
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }
}
