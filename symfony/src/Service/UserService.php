<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\Repository\AdminRepository;

/**
 * Class UserService
 */
class UserService implements UserServiceInterface
{
    private $adminRepository;

    /**
     * UserService constructor
     * @param AdminRepository $adminRepository
     */
    public function __construct(
        AdminRepository $adminRepository
    ) {
        $this->adminRepository = $adminRepository;
    }

    /**
     * fetchUserInfo
     * @param string $username
     * @return array
     */
    public function fetchUserInfo(string $username): array
    {
        $response = $this->adminRepository->fetchUserInfoFromUsername($username);
        if ($response) {
            return [
                'admin_id' => $response->getId(),
                'name' => $response->getName(),
                'active' => $response->getActive(),
            ];
        } else {
            return [];
        }
    }
}
