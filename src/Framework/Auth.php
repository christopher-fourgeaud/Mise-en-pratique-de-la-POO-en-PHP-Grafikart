<?php

namespace Framework;

use Framework\Auth\User;

interface Auth
{
    /**
     * Récupère le User
     *
     * @return User|null
     */
    public function getUser(): ?User;
}
