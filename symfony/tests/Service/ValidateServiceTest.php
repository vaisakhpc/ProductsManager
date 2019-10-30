<?php
namespace Tests\App\Service;

use App\Service\ValidateService;
use PHPUnit\Framework\TestCase;
use App\Repository\ProductsRepository;

/**
 * Class ValidateServiceTest
 */
class ValidateServiceTest extends TestCase
{
    private function getObject($methods)
    {
        $productRepository = $this->createMock(ProductsRepository::class);
        if (!empty($methods)) {
            foreach ($methods as $key => $value) {
                $productRepository->expects($this->any())
                    ->method($key)
                    ->willReturn($value);
            }
        }

        return new ValidateService(
            $productRepository
        );
    }

    private function getSampleProducts($single = false)
    {
        $products = [
            [
                "id" => "1",
                "sku" => "SKU001",
                "name" => "I phone X",
                "description" => "Apple company phone",
                "price" => "1999.99",
                "bundle" => 1,
                "discount_id" => 1,
                "type" => 0,
                "value" => "10",
                "label" => '$10 Discount',
            ],
            [
                "id" => "2",
                "sku" => "SKU002",
                "name" => "I phone XI",
                "description" => "Apple company phone",
                "price" => "2189.99",
                "bundle" => 0,
                "discount_id" => 1,
                "type" => 1,
                "value" => "2",
                "label" => '2% Discount',
            ],
        ];
        return $single ? $products[0] : $products;
    }

    private function getSampleProductRequest(
        $empty = false,
        $invalidPrice = false,
        $invalidDiscount = false,
        $invalidType = false
    ) {
        return [
            "sku" => $empty ? "" : "SKU0006",
            "name" => "Samsung Galaxy S10",
            "description" => "Samsung Galaxy S10. Brand new phone",
            "price" => $invalidPrice ? "wrong price" : "299.99",
            "discount" => [
                "type" => $invalidType ? "Invalid Type!" : "Fixed",
                "value" => $invalidDiscount ? "wrong discount value" : "25",
                "label" => "fixed amount 25 discount for S10 new"
            ],
        ];
    }

    private function getSampleSetDiscountRequest($empty = false, $flag = true, $invalidType = false)
    {
        return [
            "sku" => $empty ? "" : "SKU003",
            "type" => $invalidType ? "wrong type" : "Percent",
            "value" => $flag ? "87" : "invalid price",
            "label" => "fixed amount 25 discount for S10 new",
        ];
    }

    public function testValidateProductRequest()
    {
        $methodsToMock = [
            'fetchProductBySku' => [],
        ];
        $obj = $this->getObject($methodsToMock);
        $request = [$this->getSampleProductRequest()];
        $response = $obj->validateProductRequest($request);
        $this->assertEmpty($response);
    }

    public function testValidateProductRequestWithExistingProduct()
    {
        $methodsToMock = [
            'fetchProductBySku' => $this->getSampleProducts(true),
        ];
        $obj = $this->getObject($methodsToMock);
        $request = [$this->getSampleProductRequest()];
        $response = $obj->validateProductRequest($request);
        $this->assertNotEmpty($response);
    }

    public function testInValidProductsWithEmptyValues()
    {
        $methodsToMock = [
            'fetchProductBySku' => [],
        ];
        $obj = $this->getObject($methodsToMock);
        $request = [$this->getSampleProductRequest(true)];
        $response = $obj->validateProductRequest($request);
        $this->assertNotEmpty($response);
    }

    public function testInValidProductsWithInvalidPrice()
    {
        $methodsToMock = [
            'fetchProductBySku' => [],
        ];
        $obj = $this->getObject($methodsToMock);
        $request = [$this->getSampleProductRequest(false, true)];
        $response = $obj->validateProductRequest($request);
        $this->assertNotEmpty($response);
    }

    public function testInValidProductsWithInvalidDiscountValue()
    {
        $methodsToMock = [
            'fetchProductBySku' => [],
        ];
        $obj = $this->getObject($methodsToMock);
        $request = [$this->getSampleProductRequest(false, false, true)];
        $response = $obj->validateProductRequest($request);
        $this->assertNotEmpty($response);
    }

    public function testInValidProductsWithInvalidDiscountType()
    {
        $methodsToMock = [
            'fetchProductBySku' => [],
        ];
        $obj = $this->getObject($methodsToMock);
        $request = [$this->getSampleProductRequest(false, false, false, true)];
        $response = $obj->validateProductRequest($request);
        $this->assertNotEmpty($response);
    }

    public function testValidateDiscountRequest()
    {
        $obj = $this->getObject([]);
        $request = $this->getSampleSetDiscountRequest(false, true);
        $response = $obj->validateDiscountRequest($request);
        $this->assertEmpty($response);
    }

    public function testInvalidValidateDiscountRequestWithEmptyValues()
    {
        $obj = $this->getObject([]);
        $request = $this->getSampleSetDiscountRequest(true, true);
        $response = $obj->validateDiscountRequest($request);
        $this->assertNotEmpty($response);
    }

    public function testInvalidValidateDiscountRequestWithInvalidType()
    {
        $obj = $this->getObject([]);
        $request = $this->getSampleSetDiscountRequest(false, true, true);
        $response = $obj->validateDiscountRequest($request);
        $this->assertNotEmpty($response);
    }

    public function testInvalidValidateDiscountRequestWithInvalidValue()
    {
        $obj = $this->getObject([]);
        $request = $this->getSampleSetDiscountRequest(false, false, true);
        $response = $obj->validateDiscountRequest($request);
        $this->assertNotEmpty($response);
    }
}
