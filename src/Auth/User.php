<?php

namespace App\Auth;

use DateTime;
use Framework\Auth\User as AuthUser;

class User implements AuthUser
{

    public $id;

    public $username;

    public $email;

    public $password;

    public $passwordReset;

    public $passwordResetAt;


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


    /**
     * Get the value of passwordReset
     */
    public function getPasswordReset()
    {
        return $this->passwordReset;
    }

    /**
     * Set the value of passwordReset
     *
     * @return  self
     */
    public function setPasswordReset($passwordReset)
    {
        $this->passwordReset = $passwordReset;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of passwordResetAt
     */
    public function getPasswordResetAt(): DateTime
    {
        return $this->passwordResetAt;
    }

    public function setPasswordResetAt($date)
    {
        if (is_string($date)) {
            $this->passwordResetAt = new \DateTime($date);
        } else {
            $this->passwordResetAt = $date;
        }
    }
}
