<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class User implements UserInterface, EquatableInterface
{
    private $username;
    private $password;
    private $salt;
    private $roles;

    /**
     * User constructor
     * @param type $username
     * @param type $password
     * @param type $salt
     * @param array $roles
     */
    public function __construct($username, $password, $salt, array $roles)
    {
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->roles = $roles;
    }

    /**
     * getRoles
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * getPassword
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * getSalt
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * getUsername
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    // Just returning the object itself for now
    public function eraseCredentials(): User
    {
        return $this;
    }

    /**
     * isEqualTo
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}
