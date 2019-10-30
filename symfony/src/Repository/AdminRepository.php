<?php
namespace App\Repository;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;

class AdminRepository
{
    private $repository;

    private $entityManager;

    /**
     * AdminRepository constructor
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Admin::class);
        $this->entityManager = $entityManager;
    }

    /**
     * fetchUserInfoFromUsername
     * @param string $username
     * @return Admin|null
     */
    public function fetchUserInfoFromUsername(string $username):? Admin
    {
        return $this->repository->findByName($username)[0];
    }
}
