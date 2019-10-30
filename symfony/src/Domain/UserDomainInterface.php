<?php
namespace App\Domain;

interface UserDomainInterface
{
    public function fetchUserInfo(string $username): array;
}
