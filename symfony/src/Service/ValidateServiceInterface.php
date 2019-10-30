<?php

namespace App\Service;

interface ValidateServiceInterface
{
    public function validateProductRequest(array $request): array;

    public function validateDiscountRequest(array $entry): array;
}
