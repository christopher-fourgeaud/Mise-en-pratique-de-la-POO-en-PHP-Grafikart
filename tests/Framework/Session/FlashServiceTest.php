<?php

namespace Tests\Framework\Session;

use Framework\Session\ArraySession;
use Framework\Session\FlashService;
use PHPUnit\Framework\TestCase;

class FlashServiceTest extends TestCase
{
    /**
     * Instance de la session
     *
     * @var ArraySession
     */
    private $session;

    /**
     * Instance du FlashService
     *
     * @var FlashService
     */
    private $flashService;

    public function setUp(): void
    {
        $this->session = new ArraySession();
        $this->flashService = new FlashService($this->session);
    }

    public function testDeleteFlashAfterGettingIt()
    {
        $this->flashService->success('Bravo');
        $this->assertEquals('Bravo', $this->flashService->get('success'));
        $this->assertNull($this->session->get('flash'));
        $this->assertEquals('Bravo', $this->flashService->get('success'));
        $this->assertEquals('Bravo', $this->flashService->get('success'));
    }
}
