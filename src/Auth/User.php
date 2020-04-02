<?php

namespace App\Auth;

use Framework\Auth\User as AuthUser;

class User implements AuthUser
{

    public $id;

    public $username;

    public $email;

    public $password;

    /**
     * Récupère le nom d'un User
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Récupère les rôles d'un User
     *
     * @return string[]
     */
    public function getRoles(): array
    {
        return [];
    }
}
