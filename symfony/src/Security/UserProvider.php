<?php

namespace App\Security;

use App\Security\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * loadUserByUsername
     * @param string|null $username
     * @return User
     */
    public function loadUserByUsername($username = null): User
    {
        $password = $salt = "";
        $roles = [];
        if (isset($username)) {
            if ($username == 'admin') {
                $username = 'admin';
                $password = 'test';
                $salt = '';
                $roles = ['ROLE_ADMIN'];
            } else {
                $username = $username;
                $password = 'customer';
                $salt = '';
                $roles = ['ROLE_USER'];
            }
            return new User($username, $password, $salt, $roles);
        } else {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $username)
            );
        }
    }

    /**
     * refreshUser
     * @param UserInterface $user
     * @return User
     */
    public function refreshUser(UserInterface $user): User
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * supportsClass
     * @param string $class
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
