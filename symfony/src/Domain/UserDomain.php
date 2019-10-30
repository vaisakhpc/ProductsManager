<?php

namespace App\Domain;

use App\Service\UserServiceInterface;

class UserDomain implements UserDomainInterface
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * constructor
     * @param UserServiceInterface $shopService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * fetchUserInfo
     * @param string $username
     * @return array
     */
    public function fetchUserInfo(string $username): array
    {
        return $this->userService->fetchUserInfo($username);
    }
}
