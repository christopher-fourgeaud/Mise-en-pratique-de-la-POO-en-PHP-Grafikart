<?php

namespace App\Account;

use App\Auth\User as AuthUser;

class User extends AuthUser
{
    /**
     * Nom
     *
     * @var string
     */
    private $lastname;

    /**
     * Prenom
     *
     * @var string
     */
    private $firstname;

    /**
     * Role
     *
     * @var string
     */
    private $role;

    public function getRoles(): array
    {
        return [$this->role];
    }

    /**
     * Get nom
     *
     * @return  string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set nom
     *
     * @param  string  $lastname  Nom
     *
     * @return  self
     */
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return  string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set prenom
     *
     * @param  string  $firstname  Prenom
     *
     * @return  self
     */
    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get the value of role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set the value of role
     *
     * @return  self
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }
}
