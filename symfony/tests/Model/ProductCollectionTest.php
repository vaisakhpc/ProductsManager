<?php

namespace Tests\App\Model;

use App\Model\Bundle;
use App\Model\BundleCollection;
use App\Model\Discount;
use App\Model\Product;
use App\Model\ProductCollection;
use PHPUnit\Framework\TestCase;

/**
 * Class ProductCollectionTest
 */
class ProductCollectionTest extends TestCase
{
    private function getObject()
    {
        return new ProductCollection();
    }

    private function getProductObject()
    {
        $discount = $this->getDiscountObject();
        $bundles = $this->getBundleCollectionObject();
        return new Product('SKU002', 'Name', 'Description', '100.09', 'true', $discount, $bundles);
    }

    private function getDiscountObject()
    {
        return new Discount('Fixed', '100', 'Label', 'SKU001');
    }

    private function getBundleObject()
    {
        return new Bundle('SKU001', '92.99');
    }

    private function getBundleCollectionObject()
    {
        $bundle = $this->getBundleObject();
        $obj = new BundleCollection();
        $result = $obj->add($bundle);
        return $obj;
    }

    public function testAddMethod()
    {
        $product = $this->getProductObject();
        $obj = $this->getObject();
        $result = $obj->add($product);
        $this->assertInstanceOf(Product::class, $result[0]);
    }

    public function testcountMethod()
    {
        $product = $this->getProductObject();
        $obj = $this->getObject();
        $obj->add($product);
        $result = $obj->count();
        $this->assertEquals($result, 1);
    }

    public function testJsonSerialize()
    {
        $item = $this->getProductObject();
        $obj = $this->getObject();
        $obj->add($item);
        $result = $obj->jsonSerialize();
        $this->assertEquals($result, [$item]);
    }
}
