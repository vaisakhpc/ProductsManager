<?php

namespace App\Service;

use App\Repository\ProductsRepository;

/**
 * Class ValidateService
 */
class ValidateService implements ValidateServiceInterface
{
    private $productRepository;

    private $errors = [];

    const ALLOWED_DISCOUNT_TYPES = ['Fixed', 'Percent'];

    /**
     * ValidateService constructor
     * @param ProductsRepository $productRepository
     */
    public function __construct(
        ProductsRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * validateProductRequest
     * @param array $request
     * @return array
     */
    public function validateProductRequest(array $request): array
    {
        foreach ($request as $entry) {
            if (empty($entry['sku']) || empty($entry['name']) || empty($entry['price'])) {
                $this->errors[] = "Fields sku,name and price can't be empty";
            } elseif (!is_numeric($entry['price'])) {
                $this->errors[] = "Price should be numeric";
            } else {
                $response = $this->productRepository->fetchProductBySku($entry['sku']);
                if ($response) {
                    $this->errors[] = "Product with SKU - " . $entry['sku'] . " already exists";
                }
            }

            if (!empty($entry['discount'])) {
                if (!is_numeric($entry['discount']['value'])) {
                    $this->errors[] = "Discount value for product - " . $entry['sku'] . " should be numeric";
                }
                if (!in_array($entry['discount']['type'], self::ALLOWED_DISCOUNT_TYPES)) {
                    $this->errors[] = "Discount types should either Fixed or Percent";
                }
            }
        }

        return $this->errors;
    }

    /**
     * validateDiscountRequest
     * @param array $entry
     * @return array
     */
    public function validateDiscountRequest(array $entry): array
    {
        if (empty($entry['sku']) || empty($entry['type']) || empty($entry['value'])) {
            $this->errors[] = "Fields sku,type and value can't be empty";
        }
        if (!is_numeric($entry['value'])) {
            $this->errors[] = "Discount amount should be numeric";
        }
        if (!in_array($entry['type'], self::ALLOWED_DISCOUNT_TYPES)) {
            $this->errors[] = "Discount types should either Fixed or Percent";
        }

        return $this->errors;
    }
}
