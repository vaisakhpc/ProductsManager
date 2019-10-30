<?php

namespace App\Service;

interface UserServiceInterface
{
    public function fetchUserInfo(string $username): array;
}
