<?php

namespace Tests\Framework\Modules;

use stdClass;
use Framework\Router;

class WrongModule
{
    public function __construct(Router $router)
    {
        $router->get('/demo', function () {
            return new stdClass();
        }, 'demo');
    }
}
