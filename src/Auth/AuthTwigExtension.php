<?php

namespace App\Auth;

use Framework\Auth;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class AuthTwigExtension extends AbstractExtension
{

    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('current_user', [$this->auth, 'getUser'])
        ];
    }
}
