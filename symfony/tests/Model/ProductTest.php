<?php

namespace Tests\App\Model;

use App\Model\Bundle;
use App\Model\BundleCollection;
use App\Model\Discount;
use App\Model\Product;
use PHPUnit\Framework\TestCase;

/**
 * Class ProductTest
 */
class ProductTest extends TestCase
{
    private function getObject()
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

    public function testGetSku()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getSku(), "SKU002");
    }

    public function testGetName()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getName(), "Name");
    }

    public function testGetDescription()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getDescription(), "Description");
    }

    public function testGetPrice()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getPrice(), "100.09");
    }

    public function testGetBundleFlag()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getBundleFlag(), "true");
    }

    public function testGetBundles()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getBundles(), $this->getBundleCollectionObject());
    }

    public function testGetDiscount()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getDiscount(), $this->getDiscountObject());
    }

    public function testGetDiscountReturnsValidObj()
    {
        $obj = $this->getObject();
        $this->assertInstanceOf(BundleCollection::class, $obj->getBundles());
    }

    public function testGetBundlesCollectionReturnsCollectionObj()
    {
        $obj = $this->getObject();
        $this->assertInstanceOf(Discount::class, $obj->getDiscount());
    }

    public function testJsonSerialize()
    {
        $obj = $this->getObject();
        $result = $obj->jsonSerialize();
        $this->assertEquals($result['sku'], "SKU002");
        $this->assertEquals($result['name'], "Name");
        $this->assertEquals($result['description'], "Description");
        $this->assertEquals($result['price'], "100.09");
        $this->assertEquals($result['bundleFlag'], "true");
    }
}
